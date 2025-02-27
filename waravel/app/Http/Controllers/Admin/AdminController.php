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
use Illuminate\Support\Str;

class AdminController extends Controller
{

    public function dashboard()
    {
        $totalUsers = User::where('role', 'cliente')->count();
        $totalProducts = Producto::count();
        $admins = User::where('role', 'admin')
            ->where('id', '!=', auth()->id()) // Excluye al admin actual
            ->get();
        $valoraciones = Valoracion::all();
        $puntuaciones = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($valoraciones as $valoracion) {
            $puntuaciones[$valoracion->puntuacion]++;
        }

        $latestProducts = Producto::orderBy('updated_at', 'desc')->limit(10)->get();

        $latestClients = User::where('role', 'cliente')
            ->orderBy('updated_at', 'desc')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('totalUsers', 'totalProducts', 'puntuaciones', 'admins', 'latestProducts', 'latestClients'));
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

        // Crea la consulta sin obtener los resultados todavía y agrega el conteo de productos
        $query = Cliente::orderBy('updated_at', 'desc')->withCount('productos');

        // Obtén los usuarios con el rol de 'cliente'
        $users = User::where('role', 'cliente')->get();

        // Si hay búsqueda, aplica el filtro
        if (request()->has('search') && request('search') !== '') {
            $search = request('search');
            Log::info('Búsqueda: ' . $search);

            $query->whereHas('usuario', function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Aplica paginación a la consulta antes de obtener los resultados
        $clientes = $query->paginate(10);

        return view('admin.clients', compact('clientes', 'users'));
    }

    /**
     * Muestra la lista de todos los administradores registrados.
     */
    public function listAdmins()
    {
        /*Log::info("Obteniendo todos los usuarios administradores");

        Log::info("Redireccionando a la lista de administradores");
        return view('admin.dashboard', compact('admins'));*/
    }

    /**
     * Añadir un nuevo administrador al sistema.
     */
    public function addAdmin(Request $request)
    {

        return view('admin.add-admins');
        /*// Validar los datos ingresados TODO -> No debería haber selección de rol en la vista, al crear el admin debe pasar nombre, email y pass
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
        return redirect()->route('admin.admin')->with('success', 'Administrador añadido correctamente.');*/
    }

    /**
     * Ver la lista de todos los productos en la plataforma vendidos, desactivados baneados....
     */
    public function listProducts()
    {
        Log::info("Obteniendo todos los productos");

        $query = Producto::query();

        // Filtro de búsqueda
        if (request()->has('search') && request('search') !== '') {
            $search = request('search');

            $normalizedSearch = Str::lower(Str::replace(
                ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'],
                ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u'],
                $search
            ));

            Log::info('Realizando búsqueda con término normalizado', ['search' => $normalizedSearch]);

            $query->whereRaw("LOWER(REPLACE(nombre, 'á', 'a')) LIKE ?", ["%{$normalizedSearch}%"]);
        }

        // Ordenar por fecha de última modificación (updated_at)
        $productos = $query->orderBy('updated_at', 'desc')->paginate(10);

        Log::info("Redireccionando a la lista de productos");

        return view('admin.products', compact('productos'));
    }

    /**
     * Banear un producto específico.
     */
    public function banProduct($guid)
    {
        // Buscar el producto por GUID
        Log::info("Obteniendo producto por GUID");
        $producto = Producto::where('guid', $guid)->first();

        if (!$producto) {
            Log::warning('Producto no encontrado', ['guid' => $guid]);
            return redirect()->route('admin.products')->with('error', 'Producto no encontrado');
        }

        // Cambiar el estado entre "Baneado" y "Disponible"
        Log::info("Cambiando el estado del producto");
        $producto->estado = ($producto->estado === 'Baneado') ? 'Disponible' : 'Baneado';
        $producto->save();

        // Retornar a la vista de productos con un mensaje de éxito
        Log::info("Redireccionando a la vista de productos");
        return redirect()->route('admin.products')->with('success', 'Estado del producto actualizado correctamente.');
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
