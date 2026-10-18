<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Rotas de teste
|--------------------------------------------------------------------------
|
| Rotas utilizados para fazer teste basicos de requisição
|
*/

Route::get('/teste', [UserController::class, 'teste']);

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação
|--------------------------------------------------------------------------
|
| Rotas utilizadas para fazer autenticação do usuario.
| esse grupo de rotas contém registro, login e logout.
|
*/

Route::prefix('auth')->middleware('throttle:authenticate')->group(function() {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});


Route::middleware(['auth:sanctum', 'throttle:user'])->group(function() {

    Route::prefix('auth')->group(function() {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::prefix('products')->group(function() {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('{product}', [ProductController::class, 'show']);

    Route::middleware('admin')->group(function() {
        Route::get('lookup/{barcode}', [ProductController::class, 'lookup']);
        Route::patch('{product}/toggle-status', [ProductController::class, 'toggleStatus']);
        Route::post('/', [ProductController::class, 'store']);
        Route::put('{product}', [ProductController::class, 'update']);
        Route::delete('{product}', [ProductController::class, 'destroy']);
        });
    });
});