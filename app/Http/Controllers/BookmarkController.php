<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function toggle(Post $post)
    {
        $user = Auth::user();
        $existing = $user->bookmarks()->where('post_id', $post->id)->first();

        if ($existing) {
            $existing->delete();
            $saved = false;
        } else {
            $user->bookmarks()->create(['post_id' => $post->id]);
            $saved = true;
        }

        if (request()->wantsJson()) {
            return response()->json(['saved' => $saved]);
        }

        return back();
    }

    public function index()
    {
        $postIds = Auth::user()->bookmarks()->pluck('post_id');

        $posts = Post::whereIn('id', $postIds)
            ->with(['user', 'ciudad'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(12);

        return view('bookmarks.index', compact('posts'));
    }
}
