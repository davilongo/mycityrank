<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show(User $user)
    {
        $posts = $user->posts()
            ->withCount(['likes', 'comments'])
            ->orderBy('id', 'desc')
            ->paginate(12);

        $followersCount = $user->followers()->count();
        $followingCount = $user->following()->count();
        $isFollowing    = Auth::check() ? Auth::user()->isFollowing($user) : false;

        return view('users.show', compact('user', 'posts', 'followersCount', 'followingCount', 'isFollowing'));
    }

    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        $users = User::where('name', 'LIKE', "%{$q}%")
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->limit(6)
            ->get(['id', 'name', 'avatar', 'bio']);

        return response()->json($users->map(fn ($u) => [
            'id'          => $u->id,
            'name'        => $u->name,
            'avatar'      => $u->avatar,
            'posts_count' => $u->posts_count,
            'url'         => route('users.show', $u),
            'initial'     => mb_strtoupper(mb_substr($u->name, 0, 1)),
        ]));
    }

    public function discover()
    {
        $me           = Auth::user();
        $excludeIds   = $me->following()->pluck('users.id')->push($me->id);

        // Ciudades donde he publicado + ciudades de los posts que he guardado (bookmarks)
        $citiesFromPosts     = $me->posts()->pluck('ciudad_id');
        $citiesFromBookmarks = $me->bookmarks()->join('posts', 'bookmarks.post_id', '=', 'posts.id')->pluck('posts.ciudad_id');
        $myCityIds           = $citiesFromPosts->merge($citiesFromBookmarks)->filter()->unique();

        if ($myCityIds->isNotEmpty()) {
            $suggested = User::whereHas('posts', fn ($q) => $q->whereIn('ciudad_id', $myCityIds))
                ->whereNotIn('id', $excludeIds)
                ->withCount([
                    'posts',
                    'followers',
                    'posts as shared_posts_count' => fn ($q) => $q->whereIn('ciudad_id', $myCityIds),
                ])
                ->orderByDesc('shared_posts_count')
                ->limit(24)
                ->get();

            $sharedCities = Ciudad::whereIn('id', $myCityIds)->pluck('nombre', 'id');
        } else {
            $suggested = User::whereNotIn('id', $excludeIds)
                ->withCount(['posts', 'followers'])
                ->having('posts_count', '>', 0)
                ->orderByDesc('posts_count')
                ->limit(24)
                ->get();

            $sharedCities = collect();
        }

        return view('users.discover', compact('suggested', 'sharedCities', 'myCityIds'));
    }

    public function edit()
    {
        return view('users.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'bio'    => 'nullable|string|max:200',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = Storage::url($request->file('avatar')->store('public/avatars'));
        } else {
            unset($validated['avatar']);
        }

        $user->update($validated);

        return redirect()->route('users.show', $user)->with('success', 'Perfil actualizado.');
    }
}
