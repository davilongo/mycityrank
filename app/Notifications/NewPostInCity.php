<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Notifications\Notification;

class NewPostInCity extends Notification
{
    public function __construct(public User $author, public Post $post) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'city_post',
            'message' => "{$this->author->name} publicó en {$this->post->ciudad->nombre}: \"{$this->post->title}\"",
            'url'     => route('posts.show', $this->post),
            'actor'   => $this->author->name,
        ];
    }
}
