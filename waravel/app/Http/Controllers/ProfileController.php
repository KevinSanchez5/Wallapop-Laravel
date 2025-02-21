<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Valoracion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
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

    public function edit(Request $request): View
    {

        Log::info('Accediendo a la página de edición del perfil');
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        Log::info('Iniciando actualización del perfil del usuario');
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            Log::info('El correo electrónico ha sido modificado, anulando la verificación de email');
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();
        Log::info('Perfil del usuario actualizado correctamente');

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
