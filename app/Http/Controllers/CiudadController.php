<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Post;
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

        $posts      = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();
        $categorias = $ciudad->posts()->distinct()->orderBy('category')->pluck('category');

        return view('ciudades.show', compact('ciudad', 'posts', 'categorias'));
    }
}
