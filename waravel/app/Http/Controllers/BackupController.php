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

    public function deleteBackup($filename)
    {
        Log::info('Borrando copia de seguridad: ' . $filename);

        if (!str_ends_with($filename, '.zip')) {
            return response()->json(['error' => 'Formato de archivo no permitido'], 400);
        }

        $filePath = storage_path("app/{$this->backupPath}{$filename}");

        if (file_exists($filePath) && is_file($filePath)) {
            if (unlink($filePath)) {
                return response()->json(['message' => 'Backup eliminado']);
            } else {
                return response()->json(['error' => 'Error al eliminar el backup'], 500);
            }
        }

        return response()->json(['error' => 'Backup no encontrado'], 404);
    }


    public function deleteAllBackups()
    {
        Log::info('Borrando todas las copias de seguridad');

        $directory = storage_path("app/{$this->backupPath}");

        if (!is_dir($directory)) {
            return response()->json(['error' => 'La carpeta de backups no existe'], 404);
        }

        $files = scandir($directory);
        $backups = array_filter($files, fn($file) =>
            !in_array($file, ['.', '..']) &&
            is_file($directory . DIRECTORY_SEPARATOR . $file) &&
            str_ends_with($file, '.zip') // Solo eliminar archivos ZIP
        );

        if (empty($backups)) {
            return response()->json(['message' => 'No hay backups para eliminar']);
        }

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
        Log::info('Restaurando copia de seguridad: ' . $filename);

        $directory = storage_path("app/{$this->backupPath}");
        $filePath = $directory . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($filePath) || !str_ends_with($filename, '.zip')) {
            return response()->json(['error' => 'Backup no encontrado o formato inválido'], 404);
        }

        $zip = new \ZipArchive();
        $extractPath = sys_get_temp_dir() . '/restore_temp';

        if (!\File::exists($extractPath)) {
            \File::makeDirectory($extractPath, 0775, true);
        }

        if ($zip->open($filePath) === true) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return response()->json(['error' => 'No se pudo extraer el backup'], 500);
        }

        $sqlFile = null;
        foreach (scandir($extractPath) as $file) {
            if (str_ends_with($file, '.sql')) {
                $sqlFile = $extractPath . DIRECTORY_SEPARATOR . $file;
                break;
            }
        }

        if (!$sqlFile) {
            return response()->json(['error' => 'No se encontró un archivo SQL en el backup'], 500);
        }

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
            escapeshellarg($sqlFile)
        );

        exec($restoreCommand . ' 2>&1', $outputRestore, $resultCodeRestore);

        if ($resultCodeRestore !== 0) {
            return response()->json(['error' => 'Error al restaurar la base de datos', 'output' => $outputRestore], 500);
        }

        $storagePublicPath = public_path('storage');
        $storageBackupPath = $extractPath . DIRECTORY_SEPARATOR . 'storage';

        if (is_dir($storageBackupPath)) {
            \File::copyDirectory($storageBackupPath, $storagePublicPath);
        }

        \File::deleteDirectory($extractPath);

        return response()->json([
            'message' => 'Backup restaurado correctamente',
            'output' => $outputRestore
        ]);
    }
}
