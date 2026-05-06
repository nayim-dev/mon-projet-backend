<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;

// ---- Routes publiques ----
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::get('/categories',      [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::get('/products',      [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// ---- Routes authentifiées ----
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    Route::get('/orders/my',  [OrderController::class, 'myOrders']);
    Route::get('/orders/{id}',[OrderController::class, 'show']);
    Route::post('/orders',    [OrderController::class, 'store']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // ---- Routes admin uniquement ----
    Route::middleware('role:admin')->group(function () {

        // Catégories
        Route::post('/categories',         [CategoryController::class, 'store']);
        Route::put('/categories/{id}',     [CategoryController::class, 'update']);
        Route::delete('/categories/{id}',  [CategoryController::class, 'destroy']);

        // Produits
        Route::post('/products',        [ProductController::class, 'store']);
        Route::match(['put', 'post'], '/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // Commandes
        Route::get('/orders',                    [OrderController::class, 'index']);
        Route::put('/orders/{id}/status',        [OrderController::class, 'updateStatus']);

        // Utilisateurs
        Route::get('/users',         [UserController::class, 'index']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });
});