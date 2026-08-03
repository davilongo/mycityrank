<?php

namespace App\Http\Controllers;

use App\Mail\AgenciaBienvenida;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make(Str::random(40)),
        ]);
        $user->forceFill(['is_agencia' => true])->save();

        $resetUrl = route('password.reset', [
            'token' => Password::broker()->createToken($user),
            'email' => $user->email,
        ]);

        Mail::to($user)->send(new AgenciaBienvenida($user, $resetUrl));

        return redirect()->route('admin.usuarios.index')->with('success', "Agencia \"{$user->name}\" creada correctamente.");
    }

    public function destroyUsuario(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403, 'No puedes eliminar tu propia cuenta.');

        $user->delete();

        return back()->with('success', "{$user->name} ha sido eliminado.");
    }
}
