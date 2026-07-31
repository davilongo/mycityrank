<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewLike extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $liker, public Post $post) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'like',
            'message' => "{$this->liker->name} le dio like a \"{$this->post->title}\"",
            'url'     => route('posts.show', $this->post),
            'actor'   => $this->liker->name,
        ];
    }
}
