<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Ciudad;
use App\Notifications\NewComment;
use App\Notifications\NewPostInCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['ciudad', 'user'])->withCount(['likes', 'comments']);

        if ($request->filled('categoria')) {
            $query->where('category', $request->categoria);
        }

        $posts = $query->orderBy('id', 'desc')->paginate(9)->withQueryString();

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
            'place_name'    => 'nullable|string|max:255',
            'content'       => 'required',
            'category'      => 'required|string|in:' . implode(',', \App\Models\Post::CATEGORIES),
            'ciudad_nombre' => 'required|string|max:100',
            'images'        => 'required|array|min:1|max:6',
            'images.*'      => 'image|max:8192',
            'lat'           => 'nullable|numeric|between:-90,90',
            'lng'           => 'nullable|numeric|between:-180,180',
            'tags'          => 'nullable|array|max:20',
            'tags.*'        => 'string|max:60',
        ]);

        $urls      = collect($request->file('images'))->map(fn ($f) => $this->compressAndStore($f))->values()->all();
        $url       = $urls[0];
        $extraUrls = count($urls) > 1 ? array_slice($urls, 1) : null;

        $ciudad   = $this->resolveCiudad($validated['ciudad_nombre']);
        $baseSlug = Str::slug($validated['title']);
        $slug     = $baseSlug;
        $counter  = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $post = Post::create([
            'title'      => $validated['title'],
            'slug'       => $slug,
            'content'    => $validated['content'],
            'image'      => $url,
            'images'     => $extraUrls,
            'category'   => $validated['category'],
            'place_name' => $validated['place_name'] ?? null,
            'tags'       => $validated['tags'] ?? null,
            'ciudad_id'  => $ciudad->id,
            'user_id'    => Auth::id(),
            'lat'        => $validated['lat'] ?? null,
            'lng'        => $validated['lng'] ?? null,
        ]);

        $post->syncHashtags();

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

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'place_name'    => 'nullable|string|max:255',
            'content'       => 'required',
            'category'      => 'required|string|in:' . implode(',', \App\Models\Post::CATEGORIES),
            'ciudad_nombre' => 'required|string|max:100',
            'images'        => 'nullable|array|max:6',
            'images.*'      => 'image|max:8192',
            'lat'           => 'nullable|numeric|between:-90,90',
            'lng'           => 'nullable|numeric|between:-180,180',
            'tags'          => 'nullable|array|max:20',
            'tags.*'        => 'string|max:60',
        ]);

        $post->title      = $validated['title'];
        $post->place_name = $validated['place_name'] ?? null;
        $post->tags       = $validated['tags'] ?? null;
        $post->content    = $validated['content'];
        $post->category   = $validated['category'];

        if ($request->hasFile('images')) {
            $urls = collect($request->file('images'))->map(fn ($f) => $this->compressAndStore($f))->values()->all();
            $post->image  = $urls[0];
            $post->images = count($urls) > 1 ? array_slice($urls, 1) : null;
        }

        $post->ciudad_id = $this->resolveCiudad($validated['ciudad_nombre'])->id;
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

    private function compressAndStore(\Illuminate\Http\UploadedFile $file): string
    {
        $src  = imagecreatefromstring(file_get_contents($file->getRealPath()));
        $w    = imagesx($src);
        $h    = imagesy($src);
        $maxW = 1200;

        if ($w > $maxW) {
            $newH = (int)($h * $maxW / $w);
            $dest = imagecreatetruecolor($maxW, $newH);
            imagefill($dest, 0, 0, imagecolorallocate($dest, 255, 255, 255));
            imagecopyresampled($dest, $src, 0, 0, 0, 0, $maxW, $newH, $w, $h);
            imagedestroy($src);
        } else {
            $dest = $src;
        }

        $dir = storage_path('app/public/images');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = uniqid('img_') . '.jpg';
        imagejpeg($dest, $dir . '/' . $filename, 80);
        imagedestroy($dest);

        return '/storage/images/' . $filename;
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
