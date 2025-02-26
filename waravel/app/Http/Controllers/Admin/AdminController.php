<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Valoracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    public function dashboard()
    {
        $totalUsers = User::count();
        $totalProducts = Producto::count();

        $valoraciones = Valoracion::all();
        $puntuaciones = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($valoraciones as $valoracion) {
            $puntuaciones[$valoracion->puntuacion]++;
        }

        $latestProducts = Producto::orderBy('updated_at', 'desc')->limit(5)->get();

        $latestClients = User::orderBy('updated_at', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalProducts', 'puntuaciones', 'latestProducts', 'latestClients'));
    }

    /**
     * Realiza un respaldo de la base de datos y lo almacena en el servidor.
     */
    public function backupDatabase() // TODO -> PENDIENTE DE QUE ESTÉ LA FUNCIÓN
    {
        // Generar un archivo SQL con el respaldo de la base de datos
        // Guardarlo en el almacenamiento de Laravel (storage/app/backups)
    }

    /**
     * Muestra la lista de todos los clientes registrados en el sistema.
     */
    public function listClients()
    {
        Log::info('Obteniendo clientes cuyo usuario tiene el rol de cliente');

        $clientes = Cliente::whereHas('usuario', function ($query) {
            $query->where('role', 'cliente'); // Filtra solo usuarios con rol "cliente"
        })->with('usuario')->get();

        return view('admin.clients', compact('clientes'));
    }

    /**
     * Muestra la lista de todos los administradores registrados.
     */
    public function listAdmins()
    {
        // Obtener todos los usuarios con rol de administrador
        Log::info("Obteniendo todos los usuarios administradores");
        $admins = User::where('role', "admin")->all();

        // Retornar una vista con la lista de administradores
        Log::info("Redireccionando a la lista de .........."); // TODO -> CAMBIAR POR LA VISTA DESEADA
        return view('admin.clients', compact('admins')); // TODO -> CAMBIAR POR LA VISTA DESEADA
    }

    /**
     * Añadir un nuevo administrador al sistema.
     */
    public function addAdmin(Request $request)
    {
        // Validar los datos ingresados TODO -> No debería haber selección de rol en la vista, al crear el admin debe pasar nombre, email y pass
        Log::info("Validando datos para crear el usuario administrador");
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'max:20', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'max:20', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$/'],
        ]);
        if ($validator->fails()) {
            Log::warning("Error de validación al crear administrador", ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear un nuevo usuario con rol de administrador
        Log::info("Creando usuario administrador");
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'admin',
        ]);

        // Redireccionar a la lista de administradores
        Log::info("Redireccionando a la lista de administradores");
        return redirect()->route('admin.admin')->with('success', 'Administrador añadido correctamente.');
    }

    /**
     * Ver la lista de todos los productos en la plataforma vendidos, desactivados baneados....
     */
    public function listProducts()
    {
        // Obtener todos los productos de la base de datos
        Log::info("Obteniendo todos los productos");
        $productos = Producto::all();

        // Retornar una vista con la lista de productos
        Log::info("Redireccionando a la lista de .........."); // TODO -> CAMBIAR POR LA VISTA DESEADA
        return view('admin.clients', compact('productos')); // TODO -> CAMBIAR POR LA VISTA DESEADA
    }

    /**
     * Banear un producto específico.
     */
    public function banProduct($guid)
    {
        // Buscar el producto por ID
        Log::info("Obteniendo producto por GUID");
        $producto = Producto::where('guid', $guid)->first();

        if (!$producto) {
            Log::warning('Producto no encontrado', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'Producto no encontrado'); // TODO -> CAMBIAR POR LA VISTA DESEADA
        }

        // Cambiar su estado a "baneado"
        Log::info("Baneando producto");
        $producto->estado = "Baneado";
        $producto->save();

        // Retornar a la vista ...
        Log::info("Redireccionando a .........."); // TODO -> CAMBIAR POR LA VISTA DESEADA
        return redirect()->route('admin.products')->with('success', 'Producto baneado correctamente.'); // TODO -> CAMBIAR POR LA VISTA DESEADA
    }

    /**
     * Eliminar un producto de forma permanente.
     */
    public function deleteProduct($guid)
    {
        // Buscar y eliminar el producto de la base de datos
        Log::info("Obteniendo producto por GUID");
        $producto = Producto::where('guid', $guid)->first();

        if (!$producto) {
            Log::warning('Producto no encontrado', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'Producto no encontrado'); // TODO -> CAMBIAR POR LA VISTA DESEADA
        }

        // Eliminar imágenes asociadas si existen // TOOD -> REVISAR QUE SE BORRAN LAS IMÁGENES
        Log::info("Eliminando imágenes asociadas al producto");
        $imagenes = $producto->imagenes;
        foreach ($imagenes as $imagen) {
            $filePath = $imagen->input('image');

            $key = array_search($filePath, $imagenes);
            if ($key === false) {
                return response()->json(['message' => 'Foto no encontrada en el producto'], 404);
            }

            Storage::disk('public')->delete($filePath);
            unset($imagenes[$key]);
        }

        // Eliminar producto
        $producto->delete();

        // Retornar a la vista ...
        Log::info("Redireccionando a .........."); // TODO -> CAMBIAR POR LA VISTA DESEADA
        return redirect()->route('admin.products')->with('success', 'Producto borrado correctamente.'); // TODO -> CAMBIAR POR LA VISTA DESEADA
    }

    /**
     * Eliminar un cliente de forma permanente.
     */
    public function deleteClient($guid)
    {
        // Buscar al cliente
        Log::info("Obteniendo cliente por GUID");
        $cliente = Cliente::where('guid', $guid)->first();

        if (!$cliente) {
            Log::warning('Cliente no encontrado', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'Cliente no encontrado'); // TODO -> CAMBIAR POR LA VISTA DESEADA
        }

        // Eliminar su cuenta
        Log::info("Eliminando cuenta del cliente");

        // Eliminar cliente
        $cliente->delete();

        // Retornar a la vista ...
        Log::info("Redireccionando a .........."); // TODO -> CAMBIAR POR LA VISTA DESEADA
        return redirect()->route('admin.clients')->with('success', 'Cliente borrado correctamente.'); // TODO -> CAMBIAR POR LA VISTA DESEADA

    }

    /**
     * Restaurar un producto baneado (volverlo a activar).
     */
    public function restoreProduct($guid)
    {
        // Buscar el producto por ID
        Log::info("Obteniendo producto por GUID");
        $producto = Producto::where('guid', $guid);

        if (!$producto) {
            Log::warning('Producto no encontrado', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'Producto no encontrado'); // TODO -> CAMBIAR POR LA VISTA DESEADA
        }

        // Cambiar su estado a "disponible"
        Log::info("Desbaneando producto");
        $producto->estado = "Desactivado";
        $producto->save();

        // Retornar a la vista ...
        Log::info("Redireccionando a .........."); // TODO -> CAMBIAR POR LA VISTA DESEADA
        return redirect()->route('admin.products')->with('success', 'Producto baneado correctamente.'); // TODO -> CAMBIAR POR LA VISTA DESEADA
    }
}
