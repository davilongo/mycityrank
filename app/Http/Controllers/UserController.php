<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        return view('users.show', compact('user', 'posts'));
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
