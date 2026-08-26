<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudViaje extends Model
{
    protected $table = 'solicitudes_viaje';

    protected $fillable = [
        'user_id',
        'ciudad_id',
        'contacto',
        'fecha_aprox',
        'nota',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }
}
