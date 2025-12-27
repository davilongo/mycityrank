<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
   function index()
    {
        $posts = Post::with(['ciudad', 'likes', 'comments'])->orderBy('id', 'desc')->paginate(10);
        return view('posts.index', compact('posts'));
    }

    function create()
    {
        $ciudades = Ciudad::all();
        return view('posts.create', compact('ciudades'));
    }

    public function store(Request $request)
{
    // VALIDAR (SIN unique que rompe)
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'content' => 'required',
        'category' => 'required|string|max:100',
        'ciudad_nombre' => 'required|string|max:100',
        'image' => 'required|image',
    ]);

    // GUARDAR IMAGEN
    $path = $request->file('image')->store('public/images');
    $url = Storage::url($path);

    // CREAR O BUSCAR CIUDAD
    $ciudad = Ciudad::firstOrCreate([
        'nombre' => ucfirst(strtolower(trim($validated['ciudad_nombre'])))
    ]);

    // GENERAR SLUG ÚNICO AUTOMÁTICO
    $slug = $validated['slug'];
    $counter = 1;
    while (Post::where('slug', $slug)->exists()) {
        $slug = $validated['slug'] . '-' . $counter++;
    }

    // CREAR POST CON TRY-CATCH
    try {
        $post = Post::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'image' => $url,
            'category' => $validated['category'],
            'ciudad_id' => $ciudad->id,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post creado correctamente');

    } catch (\Illuminate\Database\QueryException $e) {
        if ($e->getCode() === 23000) {
            return back()->withErrors(['slug' => 'Slug duplicado. Se generó: ' . $slug]);
        }
        return back()->withErrors(['error' => 'Error del servidor: ' . $e->getMessage()]);
    }
}


   function show(Post $post)
    {
        $post->load(['comments.user', 'ciudad']);  // ← CARGA COMENTARIOS + USUARIO
        return view('posts.show', compact('post'));
    }


    function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . $post->id,
            'content' => 'required',
            'category' => 'required|string|max:100',
            'ciudad_nombre' => 'required|string|max:100',
        ]);

        $post->title = $request->title;
        $post->slug = $request->slug;
        $post->content = $request->content;
        $post->category = $request->category;

        // Si hay nueva imagen
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('public/images');
            $post->image = Storage::url($image);
        }

        // Actualizar ciudad
        $ciudad = Ciudad::firstOrCreate([
            'nombre' => ucfirst(strtolower(trim($request->ciudad_nombre)))
        ]);
        $post->ciudad_id = $ciudad->id;

        $post->save();

        return redirect()->route('posts.show', $post);
    }

    function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index');
    }
    public function comment(Request $request, Post $post)
    {
        $request->validate(['body' => 'required|string|max:1000']);
        
        $post->comments()->create([
            'body' => $request->body,
            'user_id' => Auth::user()->id,
        ]);
        
        return back()->with('success', '¡Comentario publicado!');
    }

}
