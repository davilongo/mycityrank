@extends('layouts.app')

@section('contenido')
<div class="notif-page">
    <h2 class="notif-title">Notificaciones</h2>

    @forelse($notifications as $notif)
        <div class="notif-item {{ $notif->read_at ? 'notif-read' : 'notif-unread' }}">
            <div class="notif-icon">
                @if($notif->data['type'] === 'like') ❤️
                @elseif($notif->data['type'] === 'comment') 💬
                @else 👤
                @endif
            </div>
            <div class="notif-body">
                <a href="{{ $notif->data['url'] }}" class="notif-message">
                    {{ $notif->data['message'] }}
                </a>
                <span class="notif-time">{{ $notif->created_at->diffForHumans() }}</span>
            </div>
            @if(!$notif->read_at)
                <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                    @csrf
                    <button type="submit" class="notif-mark-read" title="Marcar como leída">✓</button>
                </form>
            @endif
        </div>
    @empty
        <p class="notif-empty">No tienes notificaciones todavía.</p>
    @endforelse

    <div class="pagination-wrap">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
