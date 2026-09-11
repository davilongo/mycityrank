<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function index()
    {
        $newsletters = Newsletter::orderByDesc('created_at')->paginate(20);

        return view('admin.newsletters.index', compact('newsletters'));
    }

    public function create()
    {
        return view('admin.newsletters.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $newsletter = Newsletter::create([
            'title'        => $validated['title'],
            'slug'         => $this->uniqueSlug($validated['title']),
            'excerpt'      => $validated['excerpt'] ?? null,
            'body'         => $validated['body'],
            'is_published' => $validated['is_published'],
            'published_at' => $validated['is_published'] ? now() : null,
        ]);

        return redirect()->route('admin.newsletters.index')->with('success', "\"{$newsletter->title}\" guardada correctamente.");
    }

    public function edit(Newsletter $newsletter)
    {
        return view('admin.newsletters.edit', compact('newsletter'));
    }

    public function update(Request $request, Newsletter $newsletter)
    {
        $validated = $this->validated($request);

        $newsletter->title   = $validated['title'];
        $newsletter->excerpt = $validated['excerpt'] ?? null;
        $newsletter->body    = $validated['body'];

        if ($validated['is_published'] && !$newsletter->published_at) {
            $newsletter->published_at = now();
        }
        $newsletter->is_published = $validated['is_published'];

        $newsletter->save();

        return redirect()->route('admin.newsletters.index')->with('success', "\"{$newsletter->title}\" actualizada correctamente.");
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();

        return back()->with('success', "\"{$newsletter->title}\" eliminada.");
    }

    public function yunomad()
    {
        $newsletters = Newsletter::published()
            ->orderByDesc('published_at')
            ->limit(20)
            ->get(['title', 'slug', 'excerpt', 'body', 'published_at']);

        return response()->json($newsletters);
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body'    => ['required', 'string'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        return $validated;
    }

    private function uniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug     = $baseSlug;
        $counter  = 1;

        while (Newsletter::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}
