<?php

use App\Http\Controllers\Views\ClienteControllerView;
use App\Http\Controllers\Views\ProductoControllerView;
use App\Http\Controllers\Views\ValoracionesControllerView;
use Illuminate\Support\Facades\Route;

// Ruta para la página principal
Route::get('/', [ProductoControllerView::class, 'indexVista'])->name('inicio');

Route::get('/productos', [ProductoControllerView::class, 'indexVista'])->name('productos.index');
Route::get('/producto/{guid}', [ProductoControllerView::class, 'showVista'])->name('producto.show');
Route::get('/productos/search', [ProductoControllerView::class, 'search'])->name('productos.search');
Route::get('/cliente/{guid}', [ClienteControllerView::class, 'mostrarCliente'])->name('cliente.ver');
Route::get('/clientes/{guid}/valoraciones', [ValoracionesControllerView::class, 'index'])->name('cliente.valoraciones');
Route::get('/clientes/{guid}/puntuacion', [ValoracionesControllerView::class, 'promedio'])->name('cliente.puntuacion');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/passchange', function () {
    return view('auth.passchange');
})->name('passchange');

Route::get('/pago/success', function () {
    return view('payment.success');
})->name('pago.success');
Route::get('/pago/cancelled', function () {
    return view('payment.cancelled');
})->name('payment.cancel');
Route::get('/pago/checkout', function () {
    return view('payment.checkout');
});

