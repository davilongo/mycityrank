<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    protected $fillable = [
        'user_id',
        'ciudad_id',
        'titulo',
        'descripcion',
        'imagen',
        'fecha_salida',
        'duracion_dias',
        'precio',
        'plazas',
        'contacto',
        'activo',
    ];

    protected $casts = [
        'fecha_salida' => 'date',
        'precio'       => 'decimal:2',
        'activo'       => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true)->where('fecha_salida', '>=', now()->toDateString());
    }
}
