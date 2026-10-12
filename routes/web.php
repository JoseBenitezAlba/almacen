<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Solo administradores (se definen antes para que /products/create no choque con /products/{product})
    Route::middleware('admin')->group(function () {
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);

        Route::get('products/{product}/movements/create', [StockMovementController::class, 'create'])
            ->name('movements.create');
        Route::post('products/{product}/movements', [StockMovementController::class, 'store'])
            ->name('movements.store');
    });

    // Cualquier usuario con sesión
    Route::get('/', HomeController::class)->name('home');
    Route::resource('products', ProductController::class)->only(['index', 'show']);
    Route::get('movements', [StockMovementController::class, 'index'])->name('movements.index');
});