<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Valoracion;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use function Laravel\Prompts\error;

class ProfileControllerView extends Controller
{

    // Perfil por defecto con productos

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
        Log::info('Perfil del cliente encontrado, obteniendo productos');
        $query = Producto::where('vendedor_id', $cliente->id);

        $productos = $query->orderBy('created_at', 'desc')->paginate(6);

        Log::info('Productos obtenidos correctamente, mostrando la vista del perfil');

        return view('profile.partials.mis-productos', compact('cliente', 'productos'));
    }

    // Valoraciones

    public function showReviews(){
        Log::info('Accediendo a la página de valoraciones');

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus valoraciones.');
        }

        Log::info('Autenticando usuario');
        $usuario = Auth::user();

        Log::info('Buscando el perfil del cliente en la base de datos');
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            return redirect()->route('home')->with('error', 'No se ha encontrado el perfil del cliente.');
        }

        Log::info('Perfil del cliente encontrado, obteniendo valoraciones');
        $query = Valoracion::where('clienteValorado_id', $cliente->id);

        $valoraciones = $query->orderBy('created_at', 'desc')->paginate(6);

        Log::info('Valoraciones obtenidas correctamente, mostrando la vista de valoraciones');
        return view('profile.partials.valoraciones', compact('cliente', 'valoraciones'));
    }

    // Mis Pedidos

    public function showOrders(){
        Log::info('Accediendo a la página de pedidos');

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus pedidos.');
        }

        Log::info('Autenticando usuario');
        $usuario = Auth::user();

        Log::info('Buscando el perfil del cliente en la base de datos');
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            return redirect()->route('home')->with('error', 'No se ha encontrado el perfil del cliente.');
        }

        Log::info('Perfil del cliente encontrado, obteniendo pedidos');
        $query = Venta::where('comprador->id',$cliente->id);

        Log::info($cliente->id);

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(6);

        Log::info('Pedidos obtenidos correctamente, mostrando la vista de pedidos');
        return view('profile.partials.mis-pedidos', compact('cliente', 'pedidos'));
    }

    public function showFilteredOrders(){
        Log::info('Accediendo a la página de pedidos');

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus pedidos.');
        }

        Log::info('Autenticando usuario');
        $usuario = Auth::user();

        Log::info('Buscando el perfil del cliente en la base de datos');
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            return redirect()->route('home')->with('error', 'No se ha encontrado el perfil del cliente.');
        }

        Log::info('Perfil del cliente encontrado, obteniendo pedidos');
        $query = Venta::where('comprador->id',  $cliente->id);

        if (request()->has('estado') && request('estado') !== 'Todos') {
            $query->where('estado', request('estado'));
            Log::info('Filtro por estado aplicado', ['estado' => request('estado')]);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->paginate(6);

        Log::info('Pedidos obtenidos correctamente, mostrando la vista de pedidos');
        return view('profile.partials.mis-pedidos', compact('cliente', 'pedidos'));
    }

    // Temporal
    public function showOrder($guid) {
        Log::info('Accediendo a la página de detalle del pedido');

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus pedidos.');
        }

        Log::info('Autenticando usuario');
        $usuario = Auth::user();

        Log::info('Buscando el perfil del cliente en la base de datos');
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            return redirect()->route('home')->with('error', 'No se ha encontrado el perfil del cliente.');
        }

        Log::info('Perfil del cliente encontrado, obteniendo pedidos');

        $pedido = Venta::where('guid',  $guid)->first();

        Log::info($pedido);

        return view('profile.ver-pedido', compact('pedido', 'cliente', 'usuario'));
    }

    public function edit(Request $request)
    {
        Log::info('Accediendo a la página de edición del perfil');

        $usuario = $request->user();
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            return redirect()->route('profile')->with('error', 'No se ha encontrado el perfil del cliente.');
        }

        return view('profile.edit', compact('cliente'));
    }

    public function update(Request $request)
    {
        Log::info('Iniciando actualización del perfil del usuario');

        $usuario = Auth::user();
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            Log::error('No se ha encontrado el perfil del cliente.');
            return redirect()->route('profile')->with('error', 'No se ha encontrado el perfil del cliente.');
        }

        Log::info('Validando datos del formulario');
        try {
            $validated = $request->validate([
                'nombre'                   => 'required|string|max:255',
                'apellidos'                => 'required|string|max:255',
                'telefono'                 => 'required|string|min:9|max:9',
                'direccion.calle'          => 'required|string|max:255',
                'direccion.numero'         => 'required|integer',
                'direccion.piso'           => 'nullable|integer',
                'direccion.letra'          => 'nullable|string|max:10',
                'direccion.codigoPostal'   => 'required|integer',
                'avatar'                   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            ]);
        } catch (ValidationException $e) {
            Log::error('Error de validación', ['errors' => $e->errors()]);
            throw $e;
        }
        Log::info('Validación de datos del formulario completa', ['data' => $validated]);

        Log::info('Actualizando datos del usuario', ['usuario_id' => $usuario->id]);
        $usuario->name  = $validated['nombre'];
        $usuario->save();
        Log::info('Datos del usuario actualizados correctamente');

        Log::info('Actualizando datos del cliente', ['cliente_id' => $cliente->id]);
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

        if ($request->hasFile('avatar')) {
            Log::info('Subiendo nuevo avatar');
            $avatarPath = $request->file('avatar')->store('clientes', 'public');
            $cliente->avatar = $avatarPath;
            Log::info('Avatar subido correctamente', ['avatar_path' => $avatarPath]);
        }

        $cliente->save();
        Log::info('Datos del cliente actualizados correctamente');

        Log::info('Perfil actualizado correctamente');
        return redirect()->route('profile')->with('success', 'Perfil actualizado correctamente.');
    }

    public function destroy(Request $request)
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
