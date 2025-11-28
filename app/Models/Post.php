<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = 'posts';
    protected $fillable = [
    'title',
    'slug',
    'content',
    'image',
    'category',
    'ciudad_id'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

}
