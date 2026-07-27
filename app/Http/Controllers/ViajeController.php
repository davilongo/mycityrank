<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Viaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ViajeController extends Controller
{
    public function index()
    {
        $viajes = Viaje::with(['ciudad', 'user'])
            ->activos()
            ->orderBy('fecha_salida')
            ->paginate(12);

        return view('viajes.index', compact('viajes'));
    }

    public function create()
    {
        $ciudades = Ciudad::orderBy('nombre')->get();
        return view('viajes.create', compact('ciudades'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ciudad_id'    => 'required|exists:ciudades,id',
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'imagen'       => 'nullable|image|max:4096',
            'fecha_salida' => 'required|date|after_or_equal:today',
            'duracion_dias'=> 'required|integer|min:1|max:365',
            'precio'       => 'required|numeric|min:0',
            'plazas'       => 'nullable|integer|min:1',
            'contacto'     => 'required|string|max:255',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('viajes', 'public');
        }

        $data['user_id'] = auth()->id();

        $viaje = Viaje::create($data);

        return redirect()->route('viajes.show', $viaje)->with('success', 'Viaje creado correctamente.');
    }

    public function show(Viaje $viaje)
    {
        $viaje->load(['ciudad', 'user']);
        return view('viajes.show', compact('viaje'));
    }

    public function edit(Viaje $viaje)
    {
        $this->authorize('update', $viaje);
        $ciudades = Ciudad::orderBy('nombre')->get();
        return view('viajes.edit', compact('viaje', 'ciudades'));
    }

    public function update(Request $request, Viaje $viaje)
    {
        $this->authorize('update', $viaje);

        $data = $request->validate([
            'ciudad_id'    => 'required|exists:ciudades,id',
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'imagen'       => 'nullable|image|max:4096',
            'fecha_salida' => 'required|date',
            'duracion_dias'=> 'required|integer|min:1|max:365',
            'precio'       => 'required|numeric|min:0',
            'plazas'       => 'nullable|integer|min:1',
            'contacto'     => 'required|string|max:255',
            'activo'       => 'boolean',
        ]);

        if ($request->hasFile('imagen')) {
            if ($viaje->imagen) {
                Storage::disk('public')->delete($viaje->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('viajes', 'public');
        }

        $data['activo'] = $request->boolean('activo');

        $viaje->update($data);

        return redirect()->route('viajes.show', $viaje)->with('success', 'Viaje actualizado.');
    }

    public function destroy(Viaje $viaje)
    {
        $this->authorize('delete', $viaje);
        if ($viaje->imagen) {
            Storage::disk('public')->delete($viaje->imagen);
        }
        $viaje->delete();
        return redirect()->route('viajes.index')->with('success', 'Viaje eliminado.');
    }
}
