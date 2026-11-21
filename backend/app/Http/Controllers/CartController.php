<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddToCartRequest;
use App\Actions\Cart\AddProductToCartAction;
use App\Actions\Cart\ImportListToCartAction;
use App\Actions\Cart\CheckoutCartAction;
use App\Models\Cart;
use App\Models\ShoppingList; 
use App\Models\Product; 

class CartController extends Controller
{
    public function addToCart(AddToCartRequest $request, Cart $cart, AddProductToCartAction $action)
    {
        $product = Product::findOrFail($request->product_id);

        $user = $request->user();

        $ignoreAllergens = $request->boolean('ignore_allergens');

        $quantity = (int) $request->input('quantity', 1);

        $cartItem = $action->execute($user, $cart, $product, $quantity, $ignoreAllergens);

        return response()->json([
            'ok' => true,
            'cart_item' => $cartItem,
        ], 201);
    }

    public function startCart(Request $request, ShoppingList $shoppingList, ImportListToCartAction $action)
    {
        $user = $request->user();

        $cart = $action->execute($user, $shoppingList);

        return response()->json([
            'ok' => true,
            'cart' => $cart,
        ]);
    }

    public function checkout(Request $request, Cart $cart, CheckoutCartAction $action)
    {
        $user = $request->user();

        if ($cart->user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'message' => 'Você não tem permissão para realizar esta ação.',
            ], 403);
        }

        $summary = $action->execute($request->user(), $cart);

        return response()->json([
            'ok' => true,
            'msg' => 'Compra finalizada com sucesso!',  
            'summary' => $summary
        ]);
    }
}
