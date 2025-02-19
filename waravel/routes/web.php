<?php

use App\Http\Controllers\Views\ProductoControllerView;
use Illuminate\Support\Facades\Route;

// Ruta para la página principal
Route::get('/', [ProductoControllerView::class, 'indexVista'])->name('inicio');

Route::get('/productos', [ProductoControllerView::class, 'indexVista'])->name('productos.index');
Route::get('/producto/{guid}', [ProductoControllerView::class, 'showVista'])->name('producto.show');
Route::get('/productos/search', [ProductoControllerView::class, 'search'])->name('productos.search');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/passchange', function () {
    return view('auth.passchange');
})->name('passchange');
