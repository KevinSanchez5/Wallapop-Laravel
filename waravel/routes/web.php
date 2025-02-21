<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Views\ClienteControllerView;
use App\Http\Controllers\Views\ProductoControllerView;
use App\Http\Controllers\Views\ValoracionesControllerView;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductoControllerView::class, 'indexVista'])->name('pages.home');

Route::get('/productos/search', [ProductoControllerView::class, 'search'])->name('productos.search');
Route::get('/producto/add', [ProductoControllerView::class, 'showAddForm'])->name('producto.add');
Route::post('/producto', [ProductoControllerView::class, 'store'])->name('producto.store');
Route::get('/producto/{guid}', [ProductoControllerView::class, 'showVista'])->name('producto.show');

Route::get('/cliente/{guid}', [ClienteControllerView::class, 'mostrarCliente'])->name('cliente.ver');
Route::get('/clientes/{guid}/valoraciones', [ValoracionesControllerView::class, 'index'])->name('cliente.valoraciones');
Route::get('/clientes/{guid}/puntuacion', [ValoracionesControllerView::class, 'promedio'])->name('cliente.puntuacion');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

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

require __DIR__.'/auth.php';
