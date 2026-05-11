<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    public function buscar(Request $request)
    {
        $q = trim($request->get('q', ''));

        $ciudades = Ciudad::where('nombre', 'LIKE', "%{$q}%")
            ->withCount('posts')
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->limit(8)
            ->get();

        return response()->json($ciudades);
    }

    public function show(Ciudad $ciudad, Request $request)
    {
        $query = $ciudad->posts()->with(['user'])->withCount(['likes', 'comments']);

        if ($request->filled('categoria')) {
            $query->where('category', $request->categoria);
        }

        $posts = $query->orderByDesc('likes_count')->paginate(12)->withQueryString();

        $top3 = !$request->filled('categoria')
            ? $ciudad->posts()->with(['user'])->withCount(['likes', 'comments'])
                ->orderByDesc('likes_count')->limit(3)->get()
            : collect();

        $categorias   = Post::CATEGORIES;
        $isFollowing  = auth()->check() ? auth()->user()->isFollowingCiudad($ciudad) : false;
        $followersCount = $ciudad->followers()->count();

        return view('ciudades.show', compact('ciudad', 'posts', 'top3', 'categorias', 'isFollowing', 'followersCount'));
    }
}
