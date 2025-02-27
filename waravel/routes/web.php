<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Views\CarritoControllerView;
use App\Http\Controllers\Views\ClienteControllerView;
use App\Http\Controllers\Views\ProductoControllerView;
use App\Http\Controllers\Views\ProfileControllerView;
use App\Http\Controllers\Views\ValoracionesControllerView;
use App\Http\Middleware\AdminRoleAuth;
use App\Http\Middleware\UserRoleAuth;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductoControllerView::class, 'indexVista'])->name('pages.home');

Route::get('/cliente/{guid}', [ClienteControllerView::class, 'mostrarCliente'])->name('cliente.ver');
Route::get('/clientes/{guid}/valoraciones', [ValoracionesControllerView::class, 'index'])->name('cliente.valoraciones');
Route::get('/clientes/{guid}/puntuacion', [ValoracionesControllerView::class, 'promedio'])->name('cliente.puntuacion');

Route::middleware(['auth', UserRoleAuth::class])->group(function () {
    Route::get('/profile', [ProfileControllerView::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileControllerView::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileControllerView::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileControllerView::class, 'destroy'])->name('profile.destroy');

    Route::post('/producto', [ProductoControllerView::class, 'store'])->name('producto.store');

    Route::get('/producto/add', [ProductoControllerView::class, 'showAddForm'])->name('producto.add');
    Route::get('/producto/{guid}/edit', [ProductoControllerView::class, 'edit'])->name('producto.edit');
    Route::put('/producto/{guid}', [ProductoControllerView::class, 'update'])->name('producto.update');
    Route::post('/producto/{guid}/changestatus', [ProductoControllerView::class, 'changestatus'])->name('producto.changestatus');
    Route::delete('/producto/{guid}', [ProductoControllerView::class, 'destroy'])->name('producto.destroy');
});

Route::middleware(['auth', AdminRoleAuth::class])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/clients', [AdminController::class, 'listClients'])->name('clients.list');
    Route::get('/admin/products', [AdminController::class, 'listProducts'])->name('products.list');
    Route::get('/admin/admins', [AdminController::class, 'listAdmins'])->name('admins.list');
    Route::post('/admin/admins/add', [AdminController::class, 'addAdmin'])->name('admins.add');
    Route::get('/admin/backup', [AdminController::class, 'backupDatabase'])->name('admin.backup');
});

Route::get('/productos/search', [ProductoControllerView::class, 'search'])->name('productos.search');
Route::get('/producto/{guid}', [ProductoControllerView::class, 'showVista'])->name('producto.show');

Route::get('/carrito', [CarritoControllerView::class, 'showCart'])->name('carrito');
Route::post('/carrito/addToCart', [CarritoControllerView::class, 'addToCartOrEditSetProduct'])->name('carrito.add');
Route::put('/carrito/removeOne', [CarritoControllerView::class, 'deleteOneFromCart'])->name('carrito.removeOne');
Route::put('/carrito/addOne', [CarritoControllerView::class, 'addOneToCart'])->name('carrito.addOne');
Route::delete('/carrito/deleteFromCart', [CarritoControllerView::class, 'removeFromCart'])->name('carrito.remove');

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
