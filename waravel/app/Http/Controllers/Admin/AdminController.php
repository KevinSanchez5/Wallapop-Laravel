<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Valoracion;
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

    private $backupPath = 'backups/';

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

        if ($resultCode !== 0) {
            return response()->json(['error' => 'Error al crear el backup de la base de datos'], 500);
        }

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
     * Realiza un respaldo de la base de datos y lo almacena en el servidor.
     */
    public function backupDatabase()
    {
        $response = $this->createBackup();

        if ($response->status() !== 200) {
            return redirect()->back()->with('error', 'Error al crear el backup.');
        }

        $data = $response->getData();
        $filename = $data->filename;
        $filePath = storage_path("app/{$this->backupPath}{$filename}");

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'El archivo de respaldo no se encontró.');
        }

        return response()->download($filePath);
    }

    public function listBackups()
    {
        $files = Storage::files($this->backupPath);
        $backups = array_map(fn($file) => basename($file), $files);
        return response()->json($backups);
    }

    public function restoreBackup($filename)
    {
        $backupFilePath = storage_path("app/{$this->backupPath}{$filename}");

        if (!Storage::exists("{$this->backupPath}{$filename}")) {
            return response()->json(['error' => 'El archivo de backup no existe'], 404);
        }

        // Comando para restaurar la base de datos
        $command = sprintf(
            'PGPASSWORD=%s psql -U %s -h %s -p %s -d %s -f %s',
            env('DB_PASSWORD'),
            env('DB_USERNAME'),
            env('DB_HOST'),
            env('DB_PORT'),
            env('DB_DATABASE'),
            $backupFilePath
        );

        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            return response()->json(['error' => 'Error al restaurar la base de datos'], 500);
        }

        return response()->json(['message' => 'Backup restaurado correctamente']);
    }

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

        Log::info("Administrador añadido correctamente: {$admin->email}");

        return response()->json(['message' => 'Administrador agregado con éxito.', 'admin' => $admin], 201);
    }

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

    public function deleteReview($guid)
    {
        $valoracion = Valoracion::findOrFail($guid);
        //logers
        Log::info("Eliminando valoración");
        // Eliminar la valoración y retornar a la lista de valoraciones con un mensaje de éxito
        $valoracion->delete();
        Log::info("Valoración eliminada, Redireccionando a la lista de valoraciones");
        return redirect()->route('admin.reviews')->with('success', 'Valoración eliminada correctamente.');
    }

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

    public function deleteAdmin($guid)
    {
        $admin = User::find($guid);

        if ($admin) {
            Log::info('Administrador eliminado: ' . $admin->name . ' (ID: ' . $admin->id . ')');

            $admin->delete();
            return redirect()->route('admin.dashboard')->with('success', 'Administrador eliminado.');
        } else {
            Log::warning('Intento de eliminar un administrador que no existe. GUID: ' . $guid);

            return redirect()->route('admin.dashboard')->with('error', 'Administrador no encontrado.');
        }
    }
}
