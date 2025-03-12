<?php

namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;
use App\Mail\EmailSender;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Valoracion;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{

    /**
     * Muestra el dashboard del administrador con estadísticas y datos relevantes.
     *
     * @return \Illuminate\View\View Vista del dashboard con estadísticas.
     */

    public function dashboard()
    {
        $totalUsers = User::where('role', 'cliente')->count();
        $totalProducts = Producto::count();
        $admins = User::where('role', 'admin')
            ->where('id', '!=', auth()->id())
            ->get();
        $valoraciones = Valoracion::all();
        $puntuaciones = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($valoraciones as $valoracion) {
            $puntuaciones[$valoracion->puntuacion]++;
        }

        $latestProducts = Producto::orderBy('updated_at', 'desc')->limit(10)->get();
        $latestClients = User::where('role', 'cliente')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Obtener las últimas 10 ventas
        $latestSales = Venta::orderBy('created_at', 'desc')->limit(10)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalProducts', 'puntuaciones', 'admins', 'latestProducts', 'latestClients', 'latestSales'));
    }

    private $backupPath = 'backups/';

    /**
     * Crea una copia de seguridad de la base de datos y archivos importantes.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de la operación.
     */
    public function createBackup()
    {
        Log::info('Creando copia de seguridad');

        $timestamp = date('Y-m-d_H-i-s');
        $sqlFilename = "backup_{$timestamp}.sql";
        $zipFilename = "backup_{$timestamp}.zip";

        $sqlPath = storage_path("app/{$this->backupPath}{$sqlFilename}");
        $zipPath = storage_path("app/{$this->backupPath}{$zipFilename}");
        $storagePath = public_path('storage');

        $command = sprintf(
            'PGPASSWORD=%s pg_dump -U %s -h %s -p %s %s > %s',
            env('DB_PASSWORD'),
            env('DB_USERNAME'),
            env('DB_HOST'),
            env('DB_PORT'),
            env('DB_DATABASE'),
            $sqlPath
        );

        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
            $zip->addFile($sqlPath, $sqlFilename);

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($storagePath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = 'storage/' . substr($filePath, strlen($storagePath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();
            unlink($sqlPath);
        } else {
            return response()->json(['error' => 'Error al crear el archivo ZIP'], 500);
        }

        return response()->json(['message' => 'Backup creado', 'filename' => $zipFilename]);
    }

    /**
     * Respalda la base de datos y descarga el archivo resultante.
     *
     * @return \Illuminate\Http\Response Archivo comprimido para la descarga.
     */
    public function backupDatabase()
    {
        $response = $this->createBackup();

        if ($response->status() !== 200) {
            return redirect()->back()->with('error', 'Error al crear el backup de la base de datos');
        }

        $data = $response->getData();
        $filename = $data->filename;
        $filePath = storage_path("app/{$this->backupPath}{$filename}");

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'El archivo de respaldo no se encontró.');
        }

        return response()->download($filePath);
    }

    /**
     * Muestra la lista de clientes con la opción de filtrado por nombre o email.
     *
     * @return \Illuminate\View\View Vista con la lista de clientes filtrada.
     */
    public function listClients()
    {
        Log::info('Obteniendo clientes cuyo usuario tiene el rol de cliente');

        $query = Cliente::orderBy('updated_at', 'desc')->withCount('productos');
        $users = User::where('role', 'cliente')->get();

        if (request()->has('search') && request('search') !== '') {
            $search = request('search');
            Log::info('Búsqueda: ' . $search);

            $query->whereHas('usuario', function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $clientes = $query->paginate(10);

        return view('admin.clients', compact('clientes', 'users'));
    }

    /**
     * Muestra la lista de valoraciones con la opción de filtrado por cliente o comentario.
     *
     * @return \Illuminate\View\View Vista con la lista de valoraciones filtradas.
     */
    public function listReviews()
    {
        Log::info('Obteniendo valoraciones');

        $query = Valoracion::with(['clienteValorado', 'creador'])->orderBy('created_at', 'desc');

        if (request()->has('search') && request('search') !== '') {
            $search = request('search');
            Log::info('Búsqueda: ' . $search);

            $query->whereHas('clienteValorado', function ($q) use ($search) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ['%' . mb_strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(apellido) LIKE ?', ['%' . mb_strtolower($search) . '%']);
            })
                ->orWhereRaw('LOWER(comentario) LIKE ?', ['%' . mb_strtolower($search) . '%']);
        }

        $valoraciones = $query->paginate(9);

        return view('admin.reviews', compact('valoraciones'));
    }

    /**
     * Muestra la lista de productos con la opción de filtrado por nombre.
     *
     * @return \Illuminate\View\View Vista con la lista de productos filtrada.
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

    public function listSells()
    {
        Log::info("Obteniendo todos las Ventas");

        $query = Venta::query();

        // Filtro de búsqueda
        if (request()->has('search') && request('search') !== '') {
            $search = strtolower(request('search')); // Convertimos el término de búsqueda a minúsculas

            $query->where(function ($query) use ($search) {
                $query->whereRaw("LOWER(guid) LIKE ?", ["%{$search}%"])  // Filtrar por 'guid'
                ->orWhereRaw("LOWER(estado) LIKE ?", ["%{$search}%"]) // Filtrar por 'estado'
                ->orWhereRaw("LOWER(comprador->>'guid') LIKE ?", ["%{$search}%"]) // Filtrar por 'comprador.guid' en PostgreSQL
                ->orWhereRaw("LOWER(comprador->>'nombre') LIKE ?", ["%{$search}%"]) // Filtrar por 'comprador.nombre' en PostgreSQL
                ->orWhereRaw("LOWER(comprador->>'apellido') LIKE ?", ["%{$search}%"]) // Filtrar por 'comprador.apellido' en PostgreSQL
                ->orWhereRaw("LOWER(CAST(comprador->>'id' AS TEXT)) LIKE ?", ["%{$search}%"]); // Filtrar por 'comprador.id' en PostgreSQL
            });
        }
        $ventas = $query->orderBy('updated_at', 'desc')->paginate(10);

        Log::info("Redireccionando a la lista de ventas");

        return view('admin.sells', compact('ventas'));
    }

    /**
     * Crea un nuevo administrador si no hay más de 10 administradores.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse Respuesta con el estado de la operación.
     */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $adminCount = User::where('role', 'admin')->count();
        Log::info("Intentando añadir un nuevo administrador. Total actuales: {$adminCount}");

        if ($adminCount >= 10) {
            Log::warning("Se intentó añadir un administrador, pero ya hay 10 registrados.");
            return response()->json(['message' => 'Demasiados administradores. No se pueden agregar más.'], 400);
        }

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return response()->json(['message' => 'Administrador agregado con éxito.', 'admin' => $admin], 201);
    }

    /**
     * Cambia el estado de un producto a "Baneado" o "Disponible".
     *
     * @param  string  $guid
     * @return \Illuminate\Http\RedirectResponse Redirección a la lista de productos.
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

        // Enviamos el email
        $user = User::find($producto->vendedor->usuario_id);

        Mail::to($user->email)->send(new EmailSender($user, null, $producto, 'productoBorrado'));
        Log::info('Correo de aviso enviado', ['email' => $user->email]);

        // Retornar a la vista de productos con un mensaje de éxito
        Log::info("Redireccionando a la vista de productos");
        return redirect()->route('admin.products')->with('success', 'Producto baneado correctamente.');

        // Retornar a la vista de productos con un mensaje de éxito
        Log::info("Redireccionando a la vista de productos");
        return redirect()->route('admin.products')->with('success', 'Estado del producto actualizado correctamente.');
    }

    /**
     * Elimina una valoración por GUID.
     *
     * @param  string  $guid
     * @return \Illuminate\Http\RedirectResponse Redirección a la lista de valoraciones.
     */

    public function deleteReview($guid)
    {
        $valoracion = Valoracion::where('guid', $guid)->first();

        if (!$valoracion) {
            return redirect()->route('admin.reviews')->with('error', 'Valoración no encontrada.');
        }

        Log::info("Eliminando valoración");
        $valoracion->delete();
        Log::info("Valoración eliminada, Redireccionando a la lista de valoraciones");

        return redirect()->route('admin.reviews')->with('success', 'Valoración eliminada correctamente.');
    }

    /**
     * Elimina un cliente por GUID.
     *
     * @param  string  $guid
     * @return \Illuminate\Http\RedirectResponse Redirección a la lista de clientes.
     */


    public function deleteClient($guid)
    {
        Log::info("Obteniendo cliente por GUID");
        $cliente = Cliente::where('guid', $guid)->first();

        if (!$cliente) {
            Log::warning('Cliente no encontrado', ['guid' => $guid]);
            return redirect()->route('profile')->with('error', 'Cliente no encontrado');
        }

        Log::info("Eliminando cuenta del cliente");

        $cliente->delete();

        Log::info("Redireccionando a ..........");
        return redirect()->route('admin.clients')->with('success', 'Cliente borrado correctamente.');
    }

    /**
     * Elimina un administrador de la base de datos por su GUID.
     *
     * Este método busca un administrador en la base de datos utilizando el GUID proporcionado.
     * Si el administrador es encontrado, se elimina y se registra un mensaje de éxito.
     * Si el administrador no es encontrado, se registra una advertencia y se devuelve un mensaje de error.
     *
     * @param  string  $guid El GUID del administrador que se desea eliminar.
     * @return \Illuminate\Http\RedirectResponse Redirección al dashboard del administrador con un mensaje de éxito o error.
     */

    public function deleteAdmin($guid)
    {
        $admin = User::find($guid);

        if ($admin) {
            Log::info('Administrador eliminado: ' . $admin->name . ' (ID: ' . $admin->id . ')');

            $admin->delete();
            return redirect()->route('admin.dashboard')->with('success', 'Administrador eliminado.');
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Administrador no encontrado.');
        }
    }

    public function updateStatusOfVentas()
    {
    Log::info('Iniciando actualiación masiva de ventas');

        Venta::query()->update([
            'estado' => DB::raw("
            CASE
                WHEN estado = 'Pendiente' THEN 'Procesando'
                WHEN estado = 'Procesando' THEN 'Enviado'
                WHEN estado = 'Enviado' THEN 'Entregado'
                ELSE estado
            END
            "),
        ]);

        Log::info('Finalizando actualización masiva de ventas');
        return redirect()->back()->with('success', 'Los estados de las ventas se han actualizado correctamente.');
    }
}
