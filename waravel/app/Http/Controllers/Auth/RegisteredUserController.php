<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailSender;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{

    /**
     * Muestra el formulario de registro.
     *
     * Este método redirige al usuario a la vista de registro.
     *
     * @return \Illuminate\View\View Vista del formulario de registro.
     */
    public function showRegistrationForm()
    {
        Log::info('Mostrando formulario de registro.');
        return view('auth.register');
    }

    /**
     * Registra un nuevo usuario en el sistema.
     *
     * Este método valida los datos proporcionados, crea un nuevo usuario en la base de datos,
     * asocia el cliente con su respectiva dirección y lo loguea automáticamente. Si ocurre
     * algún error durante el registro, se captura la excepción y se muestra un mensaje de error.
     *
     * @param Request $request Los datos enviados desde el formulario de registro.
     *
     * @return \Illuminate\Http\RedirectResponse Redirige al usuario al login si el registro es exitoso,
     * o muestra un mensaje de error si ocurre un fallo en el proceso.
     */
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
                'role' => 'cliente',
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
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellidos'],
                'avatar' => "clientes/avatar.png",
                'telefono' => $validated['telefono'],
                'usuario_id' => $user->id,
                'direccion' => $direccion
                ]
            );

            Mail::to($user->email)->send(new EmailSender($user, null, null, 'bienvenida'));
            Log::info('Cliente creado con éxito para usuario ID: ' . $user->id);

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
