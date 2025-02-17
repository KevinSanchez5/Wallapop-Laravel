<?php

use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

// Ruta para la página principal
Route::get('/', [ProductoController::class, 'indexVista'])->name('inicio');

Route::get('/productos', [ProductoController::class, 'indexVista'])->name('productos.index');
Route::get('/producto/{guid}', [ProductoController::class, 'showVista'])->name('producto.show');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/passchange', function () {
    return view('auth.passchange');
})->name('passchange');
