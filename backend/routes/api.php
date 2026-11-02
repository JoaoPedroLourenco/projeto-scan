<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
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
        Route::put('/me', [AuthController::class, 'update']);
        Route::get('/sessions', [AuthController::class, 'sessions']);
        Route::delete('/sessions/{tokenId}', [AuthController::class, 'revokeSession']);
        Route::delete('/sessions', [AuthController::class, 'revokeOtherSessions']);
    });

    Route::prefix('products')->group(function() {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('{product}', [ProductController::class, 'show']);

    Route::middleware('role:admin,manager')->group(function() {
        Route::get('lookup/{barcode}', [ProductController::class, 'lookup']);
        Route::patch('{product}/toggle-status', [ProductController::class, 'toggleStatus']);
        Route::post('/', [ProductController::class, 'store']);
        Route::put('{product}', [ProductController::class, 'update']);
        Route::delete('{product}', [ProductController::class, 'destroy']);
        });
    });

    Route::middleware('role:admin,manager,operator')->group(function(){
        Route::prefix('stock')->group(function() {
            Route::get('/', [StockController::class, 'index']);
            Route::post('/entry', [StockController::class, 'entry']);
            Route::post('/exit', [StockController::class, 'exit']);
        });
    });
});