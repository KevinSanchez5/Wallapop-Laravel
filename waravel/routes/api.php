<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValoracionController;
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

    Route::get('/clientes', [ClienteController::class, 'index']);
    Route::get('/clientes/{id}', [ClienteController::class, 'show']);
    Route::post('/clientes', [ClienteController::class, 'store']);
    Route::put('/clientes/{id}', [ClienteController::class, 'update']);
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);
    Route::post('/clientes/{id}/upload', [ClienteController::class, 'updateProfilePhoto']);
    Route::get('/clientes/favoritos/{id}', [ClienteController::class, 'searchFavorites']);
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::get('/valoraciones', [ValoracionController::class, 'index']);
    Route::get('/valoraciones/{id}', [ValoracionController::class, 'show']);
    Route::post('/valoraciones', [ValoracionController::class, 'store']);
    Route::put('/valoraciones/{id}', [ValoracionController::class, 'update']);
    Route::delete('/valoraciones/{id}', [ValoracionController::class, 'destroy']);
});
