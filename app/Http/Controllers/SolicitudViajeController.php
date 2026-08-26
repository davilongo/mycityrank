<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\SolicitudViaje;
use Illuminate\Http\Request;

class SolicitudViajeController extends Controller
{
    public function create()
    {
        return view('solicitudes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ciudad_nombre' => 'required|string|max:100',
            'pais'          => 'required|string|max:100',
            'contacto'      => $request->user() ? 'nullable|string|max:255' : 'required|string|max:255',
            'fecha_aprox'   => 'nullable|string|max:100',
            'nota'          => 'nullable|string|max:1000',
        ]);

        SolicitudViaje::create([
            'user_id'     => $request->user()?->id,
            'ciudad_id'   => $this->resolveCiudad($data['ciudad_nombre'], $data['pais'])->id,
            'contacto'    => $data['contacto'] ?? null,
            'fecha_aprox' => $data['fecha_aprox'] ?? null,
            'nota'        => $data['nota'] ?? null,
        ]);

        return redirect()->route('solicitudes.create')->with('success', '¡Gracias! Tu destino queda registrado.');
    }

    public function index()
    {
        $ciudades = Ciudad::has('solicitudesViaje')
            ->withCount('solicitudesViaje')
            ->with(['solicitudesViaje' => fn ($q) => $q->with('user')->latest()])
            ->orderByDesc('solicitudes_viaje_count')
            ->get();

        return view('solicitudes.index', compact('ciudades'));
    }

    private function resolveCiudad(string $nombre, string $pais): Ciudad
    {
        return Ciudad::firstOrCreate([
            'nombre' => ucfirst(strtolower(trim($nombre))),
            'pais'   => ucfirst(strtolower(trim($pais))),
        ]);
    }
}
