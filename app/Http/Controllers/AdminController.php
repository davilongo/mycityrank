<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    public function usuarios(Request $request)
    {
        $q = trim($request->get('q', ''));

        $usuarios = User::when($q, fn ($query) => $query
                ->where('name', 'LIKE', "%{$q}%")
                ->orWhere('email', 'LIKE', "%{$q}%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.usuarios.index', compact('usuarios', 'q'));
    }

    public function toggleAgencia(User $user)
    {
        $user->forceFill(['is_agencia' => !$user->is_agencia])->save();

        return back()->with('success', $user->is_agencia
            ? "{$user->name} ahora es una agencia y puede publicar viajes."
            : "{$user->name} ya no es una agencia.");
    }

    public function crearAgencia()
    {
        return view('admin.usuarios.create');
    }

    public function storeAgencia(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->forceFill(['is_agencia' => true])->save();

        return redirect()->route('admin.usuarios.index')->with('success', "Agencia \"{$user->name}\" creada correctamente.");
    }
}
