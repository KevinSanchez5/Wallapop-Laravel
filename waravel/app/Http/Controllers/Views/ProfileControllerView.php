<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Mail\EmailSender;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Valoracion;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use function Laravel\Prompts\error;
use Illuminate\Support\Facades\Validator;

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

    // Mis ventas

    public function showSales(){
        Log::info('Accediendo a la página de mis ventas');

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus ventas.');
        }

        Log::info('Autenticando usuario');
        $usuario = Auth::user();

        Log::info('Buscando el perfil del cliente en la base de datos');
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            return redirect()->route('home')->with('error', 'No se ha encontrado el perfil del cliente.');
        }

        Log::info('Perfil del cliente encontrado, obteniendo ventas');
        $query = Venta::whereJsonContains('lineaVentas', [['vendedor' => ['id' => $cliente->id]]]);

        Log::info($query->get());
        Log::info($cliente->id);

        $ventas = $query->orderBy('created_at', 'desc')->paginate(6);

        Log::info('Ventas obtenidas correctamente, mostrando la vista de mis ventas');
        return view('profile.partials.mis-ventas', compact('cliente','ventas'));
    }

    function showFilteredSales()
    {
        Log::info('Accediendo a la página de mis ventas filtradas');

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus ventas.');
        }

        Log::info('Autenticando usuario');
        $usuario = Auth::user();

        Log::info('Buscando el perfil del cliente en la base de datos');
        $cliente = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$cliente) {
            return redirect()->route('home')->with('error', 'No se ha encontrado el perfil del cliente.');
        }

        Log::info('Perfil del cliente encontrado, obteniendo ventas');
        $query = Venta::whereJsonContains('lineaVentas', [['vendedor' => ['id' => $cliente->id]]]);

        if (request()->has('estado') && request('estado')!== 'Todos') {
            $query->where('estado', request('estado'));
            Log::info('Filtro por estado aplicado', ['estado' => request('estado')]);
        }

        $ventas = $query->orderBy('created_at', 'desc')->paginate(6);

        Log::info('Ventas obtenidas correctamente, mostrando la vista de mis ventas filtradas');
        return view('profile.partials.mis-ventas', compact('cliente','ventas'));
    }

    public function showSale($guid){
        Log::info('Accediendo a la página de detalle de una venta');

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus ventas.');
        }

        Log::info('Autenticando usuario');
        $usuario = Auth::user();

        Log::info('Buscando el perfil del cliente en la base de datos');
        $vendedor = Cliente::where('usuario_id', $usuario->id)->first();

        if (!$vendedor) {
            return redirect()->route('home')->with('error', 'No se ha encontrado el perfil del cliente.');
        }

        Log::info('Perfil del cliente encontrado, obteniendo ventas');
        $query = Venta::where('guid', $guid);

        $venta = $query->first();

        if (!$venta) {
            return redirect()->route('profile')->with('error', 'No se ha encontrado la venta.');
        }

        Log::info('Venta encontrada, verificando que le pertenece al cliente');

        $found = false;
        foreach ($venta->lineaVentas as $lineaVenta) {
            if (isset($lineaVenta['vendedor']['id']) && $lineaVenta['vendedor']['id'] === $vendedor->id) {
                Log::info('La línea de venta pertenece al vendedor');
                $found = true;
                break;
            }
        }

        Log::info('Buscardo el cliente asociado con la venta');
        $cliente = Cliente::find($venta->comprador->id);

        if (!$cliente) {
            Log::error('No se ha encontrado el comprador.');
            return redirect()->route('profile')->with('error', 'No se ha encontrado el comprador.');
        }

        Log::info('El cliente es válido, buscando su usuario');
        $usuario = User::find($cliente->usuario_id);

        Log::info($usuario);

        if (!$found) {
            Log::error('La venta no le pertenece al cliente.');
            return redirect()->route('profile')->with('error', 'No tienes permisos para ver esta venta.');
        }
        Log::info('La venta es válida y le pertenece al cliente, mostrando la vista de detalle de la venta');
        return view('profile.ver-venta', compact('venta', 'cliente', 'usuario', 'vendedor'));
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

        if (!$pedido) {
            return redirect()->route('profile')->with('error', 'No se ha encontrado el pedido.');
        }

        Log::info('Pedido encontrado, verificando que le pertenece al cliente');

        if ($cliente->id !== $pedido->comprador->id) {
            Log::error('El pedido no le pertenece al cliente.');
            return redirect()->route('profile')->with('error', 'No tienes permisos para ver este pedido.');
        }

        Log::info('Pedido válido y pertenece al cliente, mostrando la vista de detalle del pedido');

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

    public function cambioContrasenya(Request $request)
    {
        Log::info('Iniciando cambio de contraseña');

        Log::info('Validando cambio de contraseña');
        Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
            'oldPassword' => ['required', 'string', 'min:8', 'max:20'],
            'newPassword' => ['required', 'string', 'min:8', 'max:20', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
            'confirmPassword' => ['required', 'string', 'min:8', 'max:20', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
        ]);

        Log::info('Validación completada con éxito');

        $email = $request->email;
        $oldPassword = $request->oldPassword;
        $newPassword = $request->newPassword;

        Log::info('Buscando usuario por email', ['email' => $email]);
        $user = User::where('email', $email)->first();

        $response = ['success' => false, 'message' => ''];

        if (!$user) {
            Log::warning('Usuario no encontrado', ['email' => $email]);
            $response['message'] = 'Usuario no encontrado';
            return response()->json($response, 404);
        }

        if (!Hash::check($oldPassword, $user->password)) {
            Log::warning('Contraseña antigua incorrecta', ['email' => $email]);
            $response['message'] = 'La contraseña antigua es incorrecta';
            return response()->json($response, 400);
        }

        if ($newPassword !== $request->confirmPassword) {
            Log::warning('Las contraseñas no coinciden', ['email' => $email]);
            $response['message'] = 'Las contraseñas no coinciden';
            return response()->json($response, 400);
        }

        Log::info('Actualizando la contraseña');
        try {
            $user->password = Hash::make($newPassword);
            $user->password_reset_token = null;
            $user->password_reset_expires_at = null;
            $user->updated_at = now();
            $user->save();

            Log::info('Contraseña actualizada exitosamente', ['email' => $user->email]);

            $response['success'] = true;
            $response['message'] = 'Contraseña cambiada con éxito';
            $response['user'] = $user;
        } catch (\Exception $e) {
            Log::error('Error al actualizar la contraseña', ['error' => $e->getMessage()]);
            $response['message'] = 'Hubo un error al cambiar la contraseña';
            return response()->json($response, 500);
        }

        return response()->json($response);
    }

    public function eliminarPerfil(Request $request)
    {
        Log::info('Iniciando proceso de eliminación del perfil');

        $user = $request->user();

        Log::info('Enviando correo de eliminación antes de cerrar sesión');
        $this->enviarCorreoEliminarPerfil($request);

        Log::info('Desconectando al usuario');
        Auth::logout();

        Log::info('Eliminando el perfil del usuario');
        $user->delete();

        Log::info('Perfil de usuario eliminado de la base de datos');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }


    public function enviarCorreoEliminarPerfil(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            Log::warning('Usuario no autenticado');
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        try {
            Mail::to($user->email)->send(new EmailSender($user, null, null, 'eliminarPerfil'));
            Log::info('Correo de eliminación enviado', ['email' => $user->email]);
        } catch (\Exception $e) {
            Log::error('Error al enviar el correo de eliminación', [
                'email' => $user->email,
                'exception' => $e->getMessage()
            ]);
            return response()->json(['error' => $e->getMessage()], 503);
        }

        return response()->json([
            'success' => true,
            'message' => 'Correo enviado correctamente',
        ], 200);
    }


    public function findUserByEmail($email)
    {
        Log::info("Buscando usuario por email", ['email' => $email]);

        $user = User::where('email', $email)->first();

        if (!$user) {
            Log::warning('Usuario no encontrado', ['email' => $email]);
            return null;
        }

        Log::info("Usuario encontrado", ['email' => $email, 'user_id' => $user->id]);
        return $user;
    }
}
