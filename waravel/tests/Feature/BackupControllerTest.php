<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class BackupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_getAll()
    {
        $response = $this->getJson('/api/backups');
        $response->assertStatus(200)->assertJson([]);
    }

    public function test_create()
    {
        $backupPath = storage_path('app/backups/');

        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        $this->app->bind('exec', function ($command, &$output, &$resultCode) {
            $output = [];
            $resultCode = 0;
        });

        $mockZip = Mockery::mock(\ZipArchive::class);
        $mockZip->shouldReceive('open')
            ->andReturn(true);
        $mockZip->shouldReceive('addFile');
        $mockZip->shouldReceive('close');


        $this->app->instance(\ZipArchive::class, $mockZip);

        $response = $this->postJson('/api/backups/create');

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'filename']);

        $responseData = $response->json();
        $zipFilename = $responseData['filename'] ?? null;

        if ($zipFilename) {
            $this->assertFileExists($backupPath . $zipFilename);
            unlink($backupPath . $zipFilename);
        }
    }

    public function test_delete()
    {
        $backupPath = storage_path('app/backups/');
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        $filePath = $backupPath . 'test.zip';
        file_put_contents($filePath, 'dummy content');

        $this->assertFileExists($filePath);

        $response = $this->deleteJson('/api/backups/delete/test.zip');

        $response->assertStatus(200)->assertJson(['message' => 'Backup eliminado']);

        $this->assertFileDoesNotExist($filePath);
    }

    public function test_delete_fails()
    {
        $response = $this->deleteJson('/api/backups/nonexistent.zip');
        $response->assertStatus(404);
    }

    public function test_delete_all()
    {
        $backupPath = storage_path('app/backups/');

        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        $file1 = $backupPath . 'test1.zip';
        $file2 = $backupPath . 'test2.zip';
        file_put_contents($file1, 'dummy content');
        file_put_contents($file2, 'dummy content');

        $this->assertFileExists($file1);
        $this->assertFileExists($file2);

        $response = $this->deleteJson('/api/backups/delete-all');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Todos los backups han sido eliminados']);

        $this->assertFileDoesNotExist($file1);
        $this->assertFileDoesNotExist($file2);
    }

    private function mockCommandExecution($expectedCommand, $output, $resultCode)
    {
        $this->app->bind('exec', function ($command, &$outputMock, &$resultCodeMock) use ($expectedCommand, $output, $resultCode) {
            if ($command === $expectedCommand) {
                $outputMock = $output;
                $resultCodeMock = $resultCode;
                return true;
            }
            return false;
        });
    }

    public function test_getAllBackups()
    {
        $backupPath = storage_path('app/backups/');
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        $filename = 'test_backup.zip';
        file_put_contents($backupPath . $filename, 'dummy content');

        $response = $this->getJson('/api/backups');

        $response->assertStatus(200)
            ->assertJson([$filename]);

        unlink($backupPath . $filename);
    }

    public function test_deleteBackup_not_found()
    {
        $response = $this->deleteJson('/api/backups/delete/nonexistent.zip');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Backup no encontrado']);
    }

    public function test_backup_directory_creation()
    {
        $backupPath = storage_path('app/backups');
        if (file_exists($backupPath)) {
            rmdir($backupPath);
        }

        $this->assertDirectoryDoesNotExist($backupPath);

        $response = $this->getJson('/api/backups');

        $this->assertDirectoryExists($backupPath);

        $response->assertStatus(200)
            ->assertJson([]);
    }

    public function test_deleteBackup_invalid_format()
    {
        $backupPath = storage_path('app/backups/');
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        $filename = 'test_backup.txt';
        file_put_contents($backupPath . $filename, 'dummy content');

        $response = $this->deleteJson('/api/backups/delete/' . $filename);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Formato de archivo no permitido']);

        unlink($backupPath . $filename);
    }
}
