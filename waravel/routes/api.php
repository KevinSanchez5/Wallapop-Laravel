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
    Route::get('/productos/{id}', [ProductoController::class, 'show']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::put('/productos/{id}', [ProductoController::class, 'update']);
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);
    Route::post('/productos/{id}/upload', [ProductoController::class, 'addListingPhoto']);
    Route::delete('/productos/{id}/removeListingPhoto', [ProductoController::class, 'deleteListingPhoto']);

    Route::get('/clientes', [ClienteController::class, 'index']);
    Route::get('/clientes/{id}', [ClienteController::class, 'show']);
    Route::post('/clientes', [ClienteController::class, 'store']);
    Route::put('/clientes/{id}', [ClienteController::class, 'update']);
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);
    Route::post('/clientes/{id}/upload', [ClienteController::class, 'updateProfilePhoto']);
    Route::get('/clientes/{id}/favoritos', [ClienteController::class, 'searchFavorites']);
    Route::post('/clientes/{id}/favoritos', [ClienteController::class, 'addToFavorites']);
    Route::delete('/clientes/{id}/favoritos', [ClienteController::class, 'removeFromFavorites']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::get('/users/email/{email}', [UserController::class, 'showEmail']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('/users/correo-codigo', [UserController::class, 'enviarCorreoRecuperarContrasenya']);
    Route::post('/users/verificar-codigo', [UserController::class, 'verificarCodigoCambiarContrasenya']);
    Route::get('/users/verificar-correo/{email}', [UserController::class, 'validarEmail']);

    Route::get('/valoraciones', [ValoracionController::class, 'index']);
    Route::get('/valoraciones/{id}', [ValoracionController::class, 'show']);
    Route::post('/valoraciones', [ValoracionController::class, 'store']);
    Route::put('/valoraciones/{id}', [ValoracionController::class, 'update']);
    Route::delete('/valoraciones/{id}', [ValoracionController::class, 'destroy']);

    Route::get('/ventas', [VentaController::class, 'index']);
    Route::get('/ventas/{id}', [VentaController::class, 'show']);
    Route::post('/ventas', [VentaController::class, 'store']);
    Route::put('/ventas/{id}', [VentaController::class, 'update']);
    Route::delete('/ventas/{id}', [VentaController::class, 'destroy']);

    Route::post('/crear-sesion-pago',[PagoController::class, 'crearSesionPago']);

    Route::get('/backups', [BackupController::class, 'getAllBackups']);
    Route::post('/backups/create', [BackupController::class, 'createBackup']);
    Route::delete('/backups/delete-all', [BackupController::class, 'deleteAllBackups']);
    Route::delete('/backups/delete/{filename}', [BackupController::class, 'deleteBackup']);
    Route::post('/backups/restore/{filename}', [BackupController::class, 'restoreBackup']);
});
