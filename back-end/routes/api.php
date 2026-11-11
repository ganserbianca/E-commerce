<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AudienceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// IDs numéricos
Route::pattern('id', '[0-9]+');
Route::pattern('productId', '[0-9]+');

// ROTAS PÚBLICAS
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('produtos', ProductController::class)
    ->only(['index', 'show'])
    ->parameters(['produtos' => 'id']);

Route::apiResource('categorias', CategoryController::class)
    ->only(['index', 'show'])
    ->parameters(['categorias' => 'id']);

Route::apiResource('marcas', BrandController::class)
    ->only(['index', 'show'])
    ->parameters(['marcas' => 'id']);

Route::apiResource('publicos', AudienceController::class)
    ->only(['index', 'show'])
    ->parameters(['publicos' => 'id']);

// USUÁRIOS AUTENTICADOS
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);


    // CARRINHO
    Route::get('/carrinho', [CartController::class, 'index']);
    Route::post('/carrinho/itens', [CartController::class, 'store']);
    Route::patch('/carrinho/itens/{productId}', [CartController::class, 'update']);
    Route::delete('/carrinho/itens/{productId}', [CartController::class, 'destroy']);


    // ENDEREÇOS
    Route::apiResource('enderecos', AddressController::class)
        ->parameters(['enderecos' => 'id'])
        ->only(['index', 'show', 'store', 'update', 'destroy']);

    // PEDIDOS
    Route::apiResource('pedidos', OrderController::class)
        ->only(['index', 'show', 'store'])
        ->parameters(['pedidos' => 'id']);

    Route::delete('/pedidos/{id}', [OrderController::class, 'cancel']);
    Route::post('/pedidos/{id}/pay', [OrderController::class, 'pay']);

    // PERFIL
    Route::get('/perfil', [UserController::class, 'profile']);
    Route::put('/perfil', [UserController::class, 'updateProfile']);
    Route::patch('/perfil/senha', [UserController::class, 'updatePassword']);
});

// ADMINISTRADORES
Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    Route::apiResource('produtos', ProductController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['produtos' => 'id']);

    Route::apiResource('categorias', CategoryController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['categorias' => 'id']);

    Route::apiResource('marcas', BrandController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['marcas' => 'id']);

    Route::apiResource('publicos', AudienceController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['publicos' => 'id']);

    Route::patch('/pedidos/{id}/status', [OrderController::class, 'updateStatus']);
});
