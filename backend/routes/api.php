<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AllergenController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\ShoppingListItemController;
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

    Route::get('/allergens', [AllergenController::class, 'index']);

    // Cuida do index, store, show, update e destroy das listas
    Route::apiResource('shopping-lists', ShoppingListController::class);

    // Cuida do store, update e destroy dos itens de uma lista específica
    Route::apiResource('shopping-lists.items', ShoppingListItemController::class)->only([
        'store', 'update', 'destroy'
    ]);
    Route::post('shopping-lists/{shoppingList}/start-cart', [CartController::class, 'startCart'])->whereNumber('shoppingList');

    Route::prefix('auth')->group(function() {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'update']);
        Route::get('/sessions', [AuthController::class, 'sessions']);
        Route::delete('/sessions/{tokenId}', [AuthController::class, 'revokeSession']);
        Route::delete('/sessions', [AuthController::class, 'revokeOtherSessions']);
        Route::put('/me/allergens', [AuthController::class, 'syncAllergens']);
    });

    Route::prefix('cart')->group(function() {
        Route::post('/{cart}/items', [CartController::class, 'addToCart'])->whereNumber('cart');
    });

    Route::prefix('products')->group(function() {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('{product}', [ProductController::class, 'show'])->whereNumber('product');

    Route::middleware('role:admin,manager')->group(function() {
        Route::get('lookup/{barcode}', [ProductController::class, 'lookup']);
        Route::patch('{product}/toggle-status', [ProductController::class, 'toggleStatus'])->whereNumber('product');
        Route::post('/', [ProductController::class, 'store']);
        Route::put('{product}', [ProductController::class, 'update'])->whereNumber('product');
        Route::delete('{product}', [ProductController::class, 'destroy'])->whereNumber('product');
        });
    });
});