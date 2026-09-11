@extends('layouts.app')

@section('title', 'Cartas YuNomad — MyCityRank')

@section('contenido')

<div style="max-width:900px;margin:0 auto;padding:32px 16px 80px;">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0 0 4px;">✉️ Cartas de YuNomad</h1>
            <p style="color:var(--text-muted);margin:0;font-size:14px;">Escribe y publica las cartas que aparecen en <a href="/yunomad/" target="_blank" rel="noopener">la landing de YuNomad</a>.</p>
        </div>
        <a href="{{ route('admin.newsletters.create') }}" class="btn-nav">+ Nueva carta</a>
    </div>

    @if($newsletters->isEmpty())
        <div class="empty-state">
            <p>Todavía no has escrito ninguna carta.</p>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach($newsletters as $newsletter)
                <div style="display:flex;align-items:center;gap:14px;padding:14px 18px;background:var(--card-bg);border-radius:12px;box-shadow:var(--shadow-sm);flex-wrap:wrap;">
                    <div style="flex:1;min-width:180px;">
                        <div style="font-weight:700;font-size:14px;">
                            {{ $newsletter->title }}
                            @if($newsletter->is_published)
                                <span class="badge-admin" style="background:#16a34a;">Publicada</span>
                            @else
                                <span class="badge-agencia">Borrador</span>
                            @endif
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);">
                            @if($newsletter->published_at)
                                Publicada el {{ $newsletter->published_at->format('d/m/Y') }}
                            @else
                                Sin publicar
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('admin.newsletters.edit', $newsletter) }}" class="btn-ghost">Editar</a>

                    <form method="POST" action="{{ route('admin.newsletters.destroy', $newsletter) }}"
                          onsubmit="return confirm('¿Seguro que quieres eliminar \'{{ $newsletter->title }}\'? Esta acción no se puede deshacer.');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-ghost" style="color:#dc2626;">Eliminar</button>
                    </form>
                </div>
            @endforeach
        </div>
        <div style="margin-top:24px;">{{ $newsletters->links() }}</div>
    @endif

</div>

@endsection
