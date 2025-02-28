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
    Route::get('/profile/myProducts', [ProfileControllerView::class, 'show'])->name('profile.products');
    Route::get('/profile/myReviews', [ProfileControllerView::class,'showReviews'])->name('profile.reviews');
    Route::get('/profile/myOrders', [ProfileControllerView::class, 'showOrders'])->name('profile.orders');
    Route::get('/profile/mySales', [ProfileControllerView::class, 'showSales'])->name('profile.sales');
    Route::get('/profile/edit', [ProfileControllerView::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileControllerView::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileControllerView::class, 'destroy'])->name('profile.destroy');

    Route::post('/producto', [ProductoControllerView::class, 'store'])->name('producto.store');
    Route::get('/producto/add', [ProductoControllerView::class, 'showAddForm'])->name('producto.add');
    Route::get('/producto/{guid}/edit', [ProductoControllerView::class, 'edit'])->name('producto.edit');
    Route::put('/producto/{guid}', [ProductoControllerView::class, 'update'])->name('producto.update');
    Route::post('/producto/{guid}/changestatus', [ProductoControllerView::class, 'changestatus'])->name('producto.changestatus');
    Route::delete('/producto/{guid}', [ProductoControllerView::class, 'destroy'])->name('producto.destroy');

    Route::get('/pedido/overview', [CarritoControllerView::class, 'showOrder'])->name('carrito.checkout');
});

Route::middleware(['auth', AdminRoleAuth::class])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/clients', [AdminController::class, 'listClients'])->name('admin.clients');

    Route::get('/admin/products', [AdminController::class, 'listProducts'])->name('admin.products');
    Route::patch('/product/ban/{guid}', [AdminController::class, 'banProduct'])->name('admin.banProduct');


    Route::get('/admin/reviews', [AdminController::class, 'listReviews'])->name('admin.reviews');
    Route::delete('/admin/reviews/{id}', [AdminController::class, 'deleteReview'])->name('admin.reviews.destroy');

    Route::get('/admin/add', [AdminController::class, 'showAddForm'])->name('admins.add.form');
    Route::post('/admin/add', [AdminController::class, 'addAdmin'])->name('admins.add');

    Route::delete('/admin/delete/{id}', [AdminController::class, 'deleteAdmin'])->name('admin.delete');

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
