<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailSender;
use App\Models\Cliente;
use App\Models\Direccion;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function showRegistrationForm()
    {
        Log::info('Mostrando formulario de registro.');
        return view('auth.register');
    }

// Registrar nuevo usuario
    public function register(Request $request)
    {
        Log::info('Intento de registro con email: ' . $request->email);

        try {
            // Validación
            $validated = $request->validate([
                'email' => ['required', 'email', 'unique:users,email'],
                'nombre' => ['required', 'string', 'max:255'],
                'apellidos' => ['required', 'string', 'max:255'],
                'direccion' => ['required', 'array'],
                'direccion.calle' => ['required', 'string'],
                'direccion.numero' => ['required', 'integer'],
                'direccion.piso' => ['nullable', 'integer'],
                'direccion.letra' => ['nullable', 'string'],
                'direccion.codigoPostal' => ['required', 'integer'],
                'telefono' => ['required', 'string', 'min:9', 'max:9'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            Log::info('Datos validados correctamente para: ' . $validated['email']);

            // Crear usuario
            $user = User::create([
                'name' => $validated['nombre'],
                'email' => $validated['email'],
                'email_verified_at' => now(),
                'password' => Hash::make($validated['password']),
            ]);

            Log::info('Usuario creado con ID: ' . $user->id);

            // Preparar la dirección como un array asociativo
            $direccion = [
                'calle' => $validated['direccion']['calle'],
                'numero' => $validated['direccion']['numero'],
                'piso' => $validated['direccion']['piso'],
                'letra' => $validated['direccion']['letra'],
                'codigoPostal' => $validated['direccion']['codigoPostal'],
            ];

            // Crear cliente con la dirección como JSON
            $cliente = Cliente::create([
                'guid' => Str::uuid(),
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellidos'],
                'avatar' => "clientes/avatar.png",
                'telefono' => $validated['telefono'],
                'usuario_id' => $user->id,
                'direccion' => $direccion
                ]
            );

            Log::info('Cliente creado con éxito para usuario ID: ' . $user->id);

            Mail::to($user->email)->send(new EmailSender($user, null, null, 'bienvenido'));
            event(new Registered($user));
            Auth::login($user);

            Log::info('Usuario logueado exitosamente: ' . $user->email);

            return redirect()->route('login')->with('status', 'Registro exitoso. Bienvenido!');
        } catch (\Exception $e) {
            Log::error('Error en el registro: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Hubo un problema con el registro.']);
        }
    }
}
