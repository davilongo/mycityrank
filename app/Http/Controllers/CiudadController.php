<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    // Buscar ciudades por nombre
    public function buscar(Request $request)
    {
        $q = $request->get('q', '');

        // Filtramos ciudades que contengan la cadena, ignorando mayúsculas/minúsculas
        $ciudades = Ciudad::where('nombre', 'LIKE', "%{$q}%")->get();

        return response()->json($ciudades);
    }
}
