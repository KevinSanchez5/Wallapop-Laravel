<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
// Mostrar formulario de registro
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Registrar nuevo usuario
    public function register(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'nombre' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'array'],
            'direccion.calle' => ['required', 'string'],
            'direccion.numero' => ['required', 'integer'],
            'direccion.piso' => ['required', 'integer'],
            'direccion.letra' => ['required', 'string'],
            'direccion.codigoPostal' => ['required', 'integer'],
            'telefono' => ['required', 'string', 'min:10', 'max:15'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $validated['nombre'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Crear cliente y asignar dirección
        $cliente = Cliente::create([
            'guid' => (string) Str::uuid(),
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellidos'],
            'avatar' => "clientes/avatar.png",
            'direccion' => $validated['direccion'],
            'telefono' => $validated['telefono'], // Añadir el teléfono
            'usuario_id' => $user->id,
        ]);

        // Disparar evento de registro
        event(new Registered($user));

        // Redirigir a la página de login
        return redirect()->route('login')->with('status', 'Registro exitoso. Inicia sesión.');
    }
}
