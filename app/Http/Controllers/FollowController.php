<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewFollower;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function toggle(User $user)
    {
        $me = Auth::user();

        if ($me->id === $user->id) {
            return back();
        }

        if ($me->isFollowing($user)) {
            $me->following()->detach($user->id);
        } else {
            $me->following()->attach($user->id);
            $user->notify(new NewFollower($me));
        }

        return back();
    }

    public function feed()
    {
        $me = Auth::user();
        $followingUserIds  = $me->following()->pluck('users.id');
        $followingCityIds  = $me->followingCiudades()->pluck('ciudades.id');

        $hasSources = $followingUserIds->isNotEmpty() || $followingCityIds->isNotEmpty();

        if ($hasSources) {
            $posts = \App\Models\Post::with(['user', 'ciudad'])
                ->withCount(['likes', 'comments'])
                ->where(function ($q) use ($followingUserIds, $followingCityIds) {
                    $q->whereIn('user_id', $followingUserIds)
                      ->orWhereIn('ciudad_id', $followingCityIds);
                })
                ->latest()
                ->paginate(12);

            $posts->getCollection()->transform(function ($post) use ($followingUserIds) {
                $post->feed_source = $followingUserIds->contains($post->user_id) ? 'user' : 'ciudad';
                return $post;
            });
        } else {
            $posts = \App\Models\Post::with(['user', 'ciudad'])
                ->withCount(['likes', 'comments'])
                ->latest()
                ->paginate(12);

            $posts->getCollection()->transform(function ($post) {
                $post->feed_source = 'discover';
                return $post;
            });
        }

        $trending = \App\Models\Post::withCount('likes')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByDesc('likes_count')
            ->limit(5)
            ->get();

        $followedCities = $me->followingCiudades()->withCount('posts')->get();

        return view('posts.feed', compact('posts', 'trending', 'hasSources', 'followedCities'));
    }
}
