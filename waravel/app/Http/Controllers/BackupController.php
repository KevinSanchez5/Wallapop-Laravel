<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    private $backupPath = 'backups/';

    public function __construct()
    {
        Log::info('Comprobando existencia de directorio para backups');
        $initialBackupPath = storage_path('app/backups');

        if (!file_exists($initialBackupPath)) {
            Log::info('Directorio de backups inexistente, creando directorio');
            mkdir($initialBackupPath, 0777, true);
        }
    }

    public function getAllBackups()
    {
        Log::info('Buscando todos los archivos de copia de seguridad');
        $directory = storage_path("app/{$this->backupPath}");
        $files = scandir($directory);
        $backups = array_filter($files, fn($file) => !in_array($file, ['.', '..']) && is_file($directory . DIRECTORY_SEPARATOR . $file));
        return response()->json(array_values($backups));
    }

    public function createBackup()
    {
        Log::info('Creando copia de seguridad');
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path("app/{$this->backupPath}{$filename}");

        $command = sprintf(
            'PGPASSWORD=%s pg_dump -U %s -h %s -p %s %s > %s',
            env('DB_PASSWORD'),
            env('DB_USERNAME'),
            env('DB_HOST'),
            env('DB_PORT'),
            env('DB_DATABASE'),
            $path
        );

        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        return $resultCode === 0
            ? response()->json(['message' => 'Backup creado', 'filename' => $filename])
            : response()->json(['error' => 'Error al crear el backup'], 500);
    }

    public function deleteBackup($filename)
    {
        Log::info('Borrand copia de seguridad: ' . $filename);
        $directory = storage_path("app/{$this->backupPath}");
        $files = scandir($directory);
        $backups = array_filter($files, fn($file) => !in_array($file, ['.', '..']) && is_file($directory . DIRECTORY_SEPARATOR . $file));
        $backups = array_values($backups);

        foreach ($backups as $backup) {
            if ($backup === $filename) {
                $filePath = $directory . DIRECTORY_SEPARATOR . $backup;
                if (unlink($filePath)) {
                    return response()->json(['message' => 'Backup eliminado']);
                } else {
                    return response()->json(['error' => 'Error al eliminar el backup'], 500);
                }
            }
        }

        return response()->json(['error' => 'Backup no encontrado'], 404);
    }


    public function deleteAllBackups()
    {
        Log::info('Borrando todas las copias de seguridad');
        $directory = storage_path("app/{$this->backupPath}");
        $files = scandir($directory);
        $backups = array_filter($files, fn($file) =>
            !in_array($file, ['.', '..']) && is_file($directory . DIRECTORY_SEPARATOR . $file)
        );
        $backups = array_values($backups);
        $errors = [];

        foreach ($backups as $backup) {
            $filePath = $directory . DIRECTORY_SEPARATOR . $backup;
            if (!unlink($filePath)) {
                $errors[] = "Error al eliminar: {$backup}";
            }
        }

        if (empty($errors)) {
            return response()->json(['message' => 'Todos los backups han sido eliminados']);
        }

        return response()->json(['error' => 'Algunos backups no se pudieron eliminar', 'details' => $errors], 500);
    }

    public function restoreBackup($filename)
    {
        Log::info('Restaurando copia de seguridad: '. $filename);
        $directory = storage_path("app/{$this->backupPath}");
        $files = scandir($directory);
        $backups = array_filter($files, fn($file) => !in_array($file, ['.', '..']) && is_file($directory . DIRECTORY_SEPARATOR . $file));
        $backups = array_values($backups);

        foreach ($backups as $backup) {
            if ($backup === $filename) {
                $filePath = $directory . DIRECTORY_SEPARATOR . $backup;

                // Vaciamos primero la base de datos para evitar problemas a la hora de restaurar los datos
                $dropCommand = sprintf(
                    'PGPASSWORD=%s psql -U %s -h %s -p %s -d %s -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"',
                    escapeshellarg(env('DB_PASSWORD')),
                    escapeshellarg(env('DB_USERNAME')),
                    escapeshellarg(env('DB_HOST')),
                    escapeshellarg(env('DB_PORT')),
                    escapeshellarg(env('DB_DATABASE'))
                );
                exec($dropCommand, $outputDrop, $resultCodeDrop);

                if ($resultCodeDrop !== 0) {
                    return response()->json(['error' => 'Error al limpiar la base de datos', 'output' => $outputDrop], 500);
                }

                $restoreCommand = sprintf(
                    'PGPASSWORD=%s psql -U %s -h %s -p %s -d %s < %s',
                    escapeshellarg(env('DB_PASSWORD')),
                    escapeshellarg(env('DB_USERNAME')),
                    escapeshellarg(env('DB_HOST')),
                    escapeshellarg(env('DB_PORT')),
                    escapeshellarg(env('DB_DATABASE')),
                    escapeshellarg($filePath)
                );

                exec($restoreCommand . ' 2>&1', $outputRestore, $resultCodeRestore);

                return $resultCodeRestore === 0
                    ? response()->json(['message' => 'Backup restaurado correctamente', 'output' => $outputRestore])
                    : response()->json(['error' => 'Error al restaurar el backup', 'output' => $outputRestore], 500);
            }
        }

        return response()->json(['error' => 'Backup no encontrado'], 404);
    }
}
