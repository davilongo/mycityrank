<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(Post $post)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $existing = Like::where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            Like::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
            ]);
        }

        return back();
    }
}
