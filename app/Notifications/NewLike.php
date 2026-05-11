<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Notifications\Notification;

class NewLike extends Notification
{
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
