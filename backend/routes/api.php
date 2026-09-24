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


Route::get('products/barcode/{barcode}', [ProductController::class, 'showByBarcode']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Rotas de Protegidas
|--------------------------------------------------------------------------
|
| Rotas que necessitam de de bearer token.
|
*/

Route::middleware(['auth:sanctum', 'throttle:user', 'admin'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('products/lookup/{barcode}', [ProductController::class, 'lookup']);
        
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus']);
    
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);
});