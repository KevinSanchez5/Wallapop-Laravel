<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValoracionController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PagoController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('api')->group(function () {
    Route::get('/productos', [ProductoController::class, 'index']);
    Route::get('/productos/{guid}', [ProductoController::class, 'show']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::put('/productos/{guid}', [ProductoController::class, 'update']);
    Route::delete('/productos/{guid}', [ProductoController::class, 'destroy']);
    Route::post('/productos/{guid}/upload', [ProductoController::class, 'addListingPhoto']);
    Route::delete('/productos/{guid}/removeListingPhoto', [ProductoController::class, 'deleteListingPhoto']);

    Route::get('/clientes', [ClienteController::class, 'index']);
    Route::get('/clientes/{guid}', [ClienteController::class, 'show']);
    Route::post('/clientes', [ClienteController::class, 'store']);
    Route::put('/clientes/{guid}', [ClienteController::class, 'update']);
    Route::delete('/clientes/{guid}', [ClienteController::class, 'destroy']);
    Route::post('/clientes/{guid}/upload', [ClienteController::class, 'updateProfilePhoto']);
    Route::get('/clientes/{guid}/favoritos', [ClienteController::class, 'searchFavorites']);
    Route::post('/clientes/{guid}/favoritos', [ClienteController::class, 'addToFavorites']);
    Route::delete('/clientes/{guid}/favoritos', [ClienteController::class, 'removeFromFavorites']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{guid}', [UserController::class, 'show']);
    Route::get('/users/email/{email}', [UserController::class, 'showEmail']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{guid}', [UserController::class, 'update']);
    Route::delete('/users/{guid}', [UserController::class, 'destroy']);
    Route::post('/users/correo-codigo', [UserController::class, 'enviarCorreoRecuperarContrasenya']);
    Route::post('/users/verificar-codigo', [UserController::class, 'verificarCodigoCambiarContrasenya']);
    Route::get('/users/verificar-correo/{email}', [UserController::class, 'validarEmail']);
    Route::patch('/users/cambio-contraseña', [UserController::class, 'cambioContrasenya']);
    Route::delete('/users/eliminar-perfil', [UserController::class, 'eliminarPerfil']);

    Route::get('/valoraciones', [ValoracionController::class, 'index']);
    Route::get('/valoraciones/{guid}', [ValoracionController::class, 'show']);
    Route::post('/valoraciones', [ValoracionController::class, 'store']);
    Route::put('/valoraciones/{guid}', [ValoracionController::class, 'update']);
    Route::delete('/valoraciones/{guid}', [ValoracionController::class, 'destroy']);

    Route::get('/ventas', [VentaController::class, 'index']);
    Route::get('/ventas/{guid}', [VentaController::class, 'show']);
    Route::post('/ventas', [VentaController::class, 'store']);
    Route::put('/ventas/{guid}', [VentaController::class, 'update']);
    Route::delete('/ventas/{guid}', [VentaController::class, 'destroy']);
    Route::put('/ventas/cancelar/{guid}', [VentaController::class, 'cancelarVenta']);
    Route::get('/venta/{guid}/pdf', [VentaController::class, 'generatePdf'])->name('pdf.venta');

    Route::get('/backups', [BackupController::class, 'getAllBackups']);
    Route::post('/backups/create', [BackupController::class, 'createBackup']);
    Route::delete('/backups/delete-all', [BackupController::class, 'deleteAllBackups']);
    Route::delete('/backups/delete/{filename}', [BackupController::class, 'deleteBackup']);
    Route::post('/backups/restore/{filename}', [BackupController::class, 'restoreBackup']);
});
