<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class NewFollower extends Notification
{
    public function __construct(public User $follower) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'    => 'follow',
            'message' => "{$this->follower->name} empezó a seguirte",
            'url'     => route('users.show', $this->follower),
            'actor'   => $this->follower->name,
        ];
    }
}
