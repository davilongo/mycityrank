<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Notifications\Notification;

class NewComment extends Notification
{
    public function __construct(public User $commenter, public Post $post) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'comment',
            'message' => "{$this->commenter->name} comentó en \"{$this->post->title}\"",
            'url'     => route('posts.show', $this->post),
            'actor'   => $this->commenter->name,
        ];
    }
}
