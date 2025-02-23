<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Valoracion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileControllerView extends Controller
{
    public function show()
    {
        Log::info('Accediendo a la página de perfil');
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tu perfil.');
        }
        Log::info('Autenticando usuario');
        $usuario = Auth::user();


        Log::info('Buscando el perfil del cliente en la base de datos');
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            return redirect()->route('home')->with('error', 'No se ha encontrado el perfil del cliente.');
        }
        Log::info('Perfil del cliente encontrado, obteniendo productos y valoraciones asociados');
        $productos = Producto::where('vendedor_id', $cliente->id)->get();

        $valoraciones = Valoracion::where('clienteValorado_id', $cliente->id)->get();

        Log::info('Productos y valoraciones obtenidos correctamente, mostrando la vista del perfil');

        return view('profile.profile', compact('cliente', 'productos', 'valoraciones'));
    }

    public function edit(Request $request)
    {
        Log::info('Accediendo a la página de edición del perfil');

        $usuario = $request->user();
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            return redirect()->route('profile')->with('error', 'No se ha encontrado el perfil del cliente.');
        }

        return view('profile.edit-profile', compact('cliente'));
    }

    public function update(Request $request)
    {
        Log::info('Iniciando actualización del perfil del usuario');

        $usuario = $request->user();
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            return redirect()->route('profile')->with('error', 'No se ha encontrado el perfil del cliente.');
        }

        $validated = $request->validate([
            'nombre'                   => 'required|string|max:255',
            'apellidos'                => 'required|string|max:255',
            'email'                    => 'required|email|unique:users,email,' . $usuario->id,
            'telefono'                 => 'required|string|min:9|max:9',
            'direccion.calle'          => 'required|string|max:255',
            'direccion.numero'         => 'required|integer',
            'direccion.piso'           => 'nullable|integer',
            'direccion.letra'          => 'nullable|string|max:10',
            'direccion.codigoPostal'   => 'required|integer',
            'avatar'                   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Actualizar datos del usuario
        $usuario->name  = $validated['nombre'];
        $usuario->email = $validated['email'];
        $usuario->save();

        // Actualizar datos del cliente
        $cliente->nombre   = $validated['nombre'];
        $cliente->apellido = $validated['apellidos'];
        $cliente->telefono = $validated['telefono'];
        $cliente->direccion = [
            'calle'         => $validated['direccion']['calle'],
            'numero'        => $validated['direccion']['numero'],
            'piso'          => $validated['direccion']['piso'],
            'letra'         => $validated['direccion']['letra'],
            'codigoPostal'  => $validated['direccion']['codigoPostal'],
        ];

        // Actualizar avatar si se ha enviado uno nuevo
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('clientes', 'public');
            $cliente->avatar = $avatarPath;
        }
        $cliente->save();

        Log::info('Perfil actualizado correctamente');

        return redirect()->route('profile')->with('success', 'Perfil actualizado correctamente.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Log::info('Iniciando proceso de eliminación de la cuenta');
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);
        Log::info('Contraseña validada correctamente para la eliminación de la cuenta');

        $user = $request->user();

        Log::info('Desconectando al usuario');
        Auth::logout();

        Log::info('Eliminando la cuenta del usuario');
        $user->delete();

        Log::info('Cuenta de usuario eliminada de la base de datos');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
