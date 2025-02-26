<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupController extends Controller{
    /**
     * Genera un backup de la aplicación (archivos + base de datos)
     */
    public function createBackup()
    {
        Artisan::call('backup:run');

        $output = Artisan::output();

        return response()->json([
            'message' => 'Backup creado con éxito',
            'output' => $output
        ]);
    }

    /**
     * Genera solo un backup de la base de datos
     */
    public function backupDatabase()
    {
        Artisan::call('backup:run --only-db');

        $output = Artisan::output();

        return response()->json([
            'message' => 'Backup de la base de datos creado con éxito',
            'output' => $output
        ]);
    }

    /**
     * Lista los backups disponibles
     */
    public function listBackups()
    {
        Artisan::call('backup:list');
        $output = Artisan::output();

        // Mejoramos la legibilidad de la respuesta:

        // Dividir la salida en líneas
        $lines = explode("\n", trim($output));

        // Filtrar la tabla de backups
        $backups = [];
        $parsingBackups = false;

        foreach ($lines as $line) {
            if (str_contains($line, 'Name') && str_contains($line, 'Disk')) {
                // Iniciar el análisis de la tabla de backups
                $parsingBackups = true;
                continue;
            }

            if ($parsingBackups && str_contains($line, '|')) {
                $columns = array_map('trim', explode('|', trim($line, '|')));
                if (count($columns) >= 7) {
                    $backups[] = [
                        'name' => $columns[0],
                        'disk' => $columns[1],
                        'reachable' => $columns[2] === '✅',
                        'healthy' => $columns[3] === '✅',
                        'backup_count' => (int) $columns[4],
                        'newest_backup' => $columns[5],
                        'used_storage' => $columns[6]
                    ];
                }
            }
        }

        return response()->json([
            'message' => 'Backup list retrieved successfully',
            'backups' => $backups
        ]);
    }



    /**
     * Elimina backups antiguos según la configuración
     */
    public function cleanBackups()
    {
        Artisan::call('backup:clean');
        $output = Artisan::output();

        // Dividir la salida en líneas
        $lines = explode("\n", trim($output));

        // Variables para extraer datos clave
        $usedStorage = null;
        $status = 'Cleanup completed successfully';
        $cleaningMessage = null;

        foreach ($lines as $line) {
            if (str_contains($line, 'Used storage after cleanup:')) {
                $usedStorage = trim(str_replace('Used storage after cleanup:', '', $line));
            }

            if (str_contains($line, 'Cleaning backups of')) {
                $cleaningMessage = trim($line);
            }

            if (str_contains($line, 'Starting cleanup...')) {
                $status = 'Cleanup started';
            }
        }

        return response()->json([
            'message' => $status,
            'cleaning' => $cleaningMessage ?? 'No cleaning message found',
            'used_storage' => $usedStorage ?? 'Unknown'
        ]);
    }


    /**
     * Restaura un backup de la base de datos desde un archivo
     */
    public function restoreDatabase($filename)
    {
        $path = storage_path("app/backup-temp/{$filename}");

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'El archivo de backup no existe.',
                'filename' => $filename
            ], 404);
        }

        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '5432');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        putenv("PGPASSWORD={$dbPass}");

        $command = "PGPASSWORD={$dbPass} psql -h {$dbHost} -p {$dbPort} -U {$dbUser} -d {$dbName} -f {$path}";

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(300); // Tiempo máximo de ejecución en segundos

        try {
            $process->mustRun();

            return response()->json([
                'message' => 'Base de datos restaurada con éxito.',
                'filename' => $filename
            ]);
        } catch (ProcessFailedException $exception) {
            return response()->json([
                'message' => 'Error al restaurar la base de datos.',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

}
