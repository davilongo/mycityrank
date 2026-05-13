<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    const CATEGORIES = [
        '🍽️ Restaurante',
        '🍺 Bar & Copas',
        '☕ Café',
        '🏛️ Monumento & Cultura',
        '🌿 Parque & Naturaleza',
        '🛍️ Tienda & Mercado',
        '🏖️ Playa',
        '🎭 Ocio & Entretenimiento',
        '🎉 Fiestas & Tradiciones',
        '🏨 Alojamiento',
        '💡 Joya Oculta',
    ];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'images',
        'category',
        'ciudad_id',
        'user_id',
        'lat',
        'lng',
    ];

    protected $casts = [
        'lat'    => 'float',
        'lng'    => 'float',
        'images' => 'array',
    ];

    public function allImages(): array
    {
        $extras = $this->images ?? [];
        return array_values(array_unique(array_merge([$this->image], $extras)));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class);
    }

    public function syncHashtags(): void
    {
        preg_match_all('/#(\w+)/u', $this->content, $matches);
        $names = array_unique(array_map('strtolower', $matches[1]));

        $ids = collect($names)->map(fn ($name) =>
            Hashtag::firstOrCreate(['name' => $name])->id
        );

        $this->hashtags()->sync($ids);
    }
}
