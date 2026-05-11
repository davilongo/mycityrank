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
        $followingIds = Auth::user()->following()->pluck('users.id');

        $posts = \App\Models\Post::whereIn('user_id', $followingIds)
            ->with(['user', 'ciudad'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(12);

        return view('posts.feed', compact('posts'));
    }
}
