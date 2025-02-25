<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'register']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('passchange', [PasswordResetController::class, 'create'])
        ->name('passchange');

    Route::post('passchange', [PasswordResetController::class, 'store'])
        ->name('passchange.store');
});

Route::middleware('auth')->group(function () {
    // Ruta de logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Rutas del cliente
    Route::middleware('cliente')->group(function () {
        Route::get('miperfil', [ClienteController::class, 'mostrarPerfil'])
            ->name('cliente.miperfil');

        Route::get('editarperfil', [ClienteController::class, 'editarPerfil'])
            ->name('cliente.editarperfil');

        Route::post('editarperfil', [ClienteController::class, 'guardarPerfil'])
            ->name('cliente.guardarperfil');

        Route::get('subirproducto', [ClienteController::class, 'mostrarFormularioProducto'])
            ->name('cliente.subirproducto');

        Route::post('subirproducto', [ClienteController::class, 'guardarProducto'])
            ->name('cliente.guardarproducto');

        Route::get('misproductos', [ClienteController::class, 'misProductos'])
            ->name('cliente.misproductos');

        Route::get('misventas', [ClienteController::class, 'misVentas'])
            ->name('cliente.misventas');
    });

    /*
    // Rutas del administrador
    Route::middleware('admin')->group(function () {
        Route::get('admin/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::get('admin/usuarios', [AdminController::class, 'gestionUsuarios'])
            ->name('admin.gestionUsuarios');

        Route::get('admin/productos', [AdminController::class, 'gestionProductos'])
            ->name('admin.gestionProductos');

        Route::get('admin/almacen', [AdminController::class, 'almacen'])
            ->name('admin.almacen');
    });*/
});
