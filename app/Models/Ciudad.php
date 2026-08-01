<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudades';

    protected $fillable = ['nombre', 'pais'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'ciudad_follows')->withTimestamps();
    }

    public function viajes()
    {
        return $this->hasMany(Viaje::class);
    }
}
