<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Ciudad;
use App\Notifications\NewComment;
use App\Notifications\NewPostInCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['ciudad', 'user'])
            ->withCount(['likes', 'comments'])
            ->orderBy('id', 'desc')
            ->paginate(9);

        $ciudadesPopulares = Ciudad::withCount('posts')
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->with(['posts' => fn ($q) => $q
                ->orderByRaw('(SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) DESC')
                ->limit(1)
            ])
            ->limit(6)
            ->get();

        return view('posts.index', compact('posts', 'ciudadesPopulares'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'slug'          => 'required|string|max:255',
            'content'       => 'required',
            'category'      => 'required|string|in:' . implode(',', \App\Models\Post::CATEGORIES),
            'ciudad_nombre' => 'required|string|max:100',
            'image'         => 'required|image|max:4096',
            'lat'           => 'nullable|numeric|between:-90,90',
            'lng'           => 'nullable|numeric|between:-180,180',
        ]);

        $path = $request->file('image')->store('public/images');
        $url  = Storage::url($path);

        $ciudad  = $this->resolveCiudad($validated['ciudad_nombre']);
        $slug    = $validated['slug'];
        $counter = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $validated['slug'] . '-' . $counter++;
        }

        $post = Post::create([
            'title'     => $validated['title'],
            'slug'      => $slug,
            'content'   => $validated['content'],
            'image'     => $url,
            'category'  => $validated['category'],
            'ciudad_id' => $ciudad->id,
            'user_id'   => Auth::id(),
            'lat'       => $validated['lat'] ?? null,
            'lng'       => $validated['lng'] ?? null,
        ]);

        $post->syncHashtags();

        // Notificar a seguidores de la ciudad
        $post->ciudad->followers()
            ->where('users.id', '!=', Auth::id())
            ->each(fn ($follower) => $follower->notify(new NewPostInCity(Auth::user(), $post)));

        return redirect()->route('posts.index')->with('success', 'Post creado correctamente');
    }

    public function show(Post $post)
    {
        $post->load(['comments.user', 'ciudad', 'likes', 'hashtags']);
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        abort_unless($this->canModify($post), 403);
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        abort_unless($this->canModify($post), 403);

        $request->validate([
            'title'         => 'required|string|max:255',
            'slug'          => 'required|string|max:255|unique:posts,slug,' . $post->id,
            'content'       => 'required',
            'category'      => 'required|string|in:' . implode(',', \App\Models\Post::CATEGORIES),
            'ciudad_nombre' => 'required|string|max:100',
            'image'         => 'nullable|image|max:4096',
            'lat'           => 'nullable|numeric|between:-90,90',
            'lng'           => 'nullable|numeric|between:-180,180',
        ]);

        $post->title    = $request->title;
        $post->slug     = $request->slug;
        $post->content  = $request->content;
        $post->category = $request->category;

        if ($request->hasFile('image')) {
            $post->image = Storage::url($request->file('image')->store('public/images'));
        }

        $post->ciudad_id = $this->resolveCiudad($request->ciudad_nombre)->id;
        $post->lat = $request->lat ?: null;
        $post->lng = $request->lng ?: null;
        $post->save();
        $post->syncHashtags();

        return redirect()->route('posts.show', $post);
    }

    public function destroy(Post $post)
    {
        abort_unless($this->canModify($post), 403);
        $post->delete();
        return redirect()->route('posts.index');
    }

    public function comment(Request $request, Post $post)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        $post->comments()->create([
            'body'    => $request->body,
            'user_id' => Auth::id(),
        ]);

        if ($post->user_id !== Auth::id()) {
            $post->user->notify(new NewComment(Auth::user(), $post));
        }

        return back()->with('success', '¡Comentario publicado!');
    }

    public function hashtag(string $name)
    {
        $hashtag = \App\Models\Hashtag::where('name', strtolower($name))->firstOrFail();

        $posts = $hashtag->posts()
            ->with(['user', 'ciudad'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(12);

        return view('posts.hashtag', compact('hashtag', 'posts'));
    }

    public function map()
    {
        $posts = Post::whereNotNull('lat')
            ->whereNotNull('lng')
            ->with(['user', 'ciudad'])
            ->get(['id', 'title', 'slug', 'image', 'lat', 'lng', 'user_id', 'ciudad_id']);

        return view('posts.mapa', compact('posts'));
    }

    private function canModify(Post $post): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return Auth::id() === $post->user_id || $user?->isAdmin();
    }

    private function resolveCiudad(string $nombre): Ciudad
    {
        return Ciudad::firstOrCreate([
            'nombre' => ucfirst(strtolower(trim($nombre)))
        ]);
    }
}
