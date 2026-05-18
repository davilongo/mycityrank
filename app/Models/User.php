<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'avatar',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    public function followingCiudades()
    {
        return $this->belongsToMany(Ciudad::class, 'ciudad_follows')->withTimestamps();
    }

    public function isFollowingCiudad(Ciudad $ciudad): bool
    {
        return $this->followingCiudades()->where('ciudad_id', $ciudad->id)->exists();
    }

    public function rankBadge(): array
    {
        $count = $this->posts_count ?? $this->loadCount('posts')->posts_count;
        return match(true) {
            $count >= 20 => ['emoji' => '🏆', 'label' => 'Leyenda Explorer',   'tier' => 'legend'],
            $count >= 10 => ['emoji' => '⭐', 'label' => 'Explorador Experto', 'tier' => 'expert'],
            $count >= 3  => ['emoji' => '🧭', 'label' => 'Explorador',         'tier' => 'mid'],
            default      => ['emoji' => '🌱', 'label' => 'Novato',             'tier' => 'novice'],
        };
    }
}
