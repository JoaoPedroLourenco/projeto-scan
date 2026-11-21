<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\AddToCartRequest;
use App\Actions\Cart\AddProductToCartAction;
use App\Actions\Cart\ImportListToCartAction;
use App\Actions\Cart\CheckoutCartAction;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ShoppingList; 
use App\Models\Product; 

class CartController extends Controller
{
    public function addToCart(AddToCartRequest $request, Cart $cart, AddProductToCartAction $action)
    {
        $product = Product::findOrFail($request->product_id);

        $user = $request->user();

        if ($cart->user_id !== $request->user()->id) {
            return response()->json([
                'ok' => false,
                'msg' => 'Você não tem permissão para realizar esta ação.',
            ], 403);
        }

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

        if ($shoppingList->user_id !== $user->id) {
            return response()->json([
                'ok' => false,
                'msg' => 'Lista de compras não encontrada.',
            ], 404);
        }

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
                'msg' => 'Você não tem permissão para realizar esta ação.',
            ], 403);
        }

        $summary = $action->execute($request->user(), $cart);

        return response()->json([
            'ok' => true,
            'msg' => 'Compra finalizada com sucesso!',  
            'summary' => $summary
        ]);
    }

    public function store(Request $request) {
        $cart = Cart::create([
            'user_id' => $request->user()->id,
            'access_token' => Str::random(20),
        ]);

        return response()->json([
            'ok' => true,
            'cart' => $cart->load('items.product'),
        ]);
    }

    public function show(Request $request, Cart $cart)
    {
        $user = $request->user();

        if ($cart->user_id !== $user->id) {
            return response()->json([
                'ok' => false,
                'msg' => 'Você não tem permissão para realizar esta ação.',
            ], 403);
        }

        return response()->json([
            'ok' => true,
            'cart' => $cart->load('items.product'),
        ]);
    }

    public function updateItem(Request $request, Cart $cart, CartItem $item) 
    {
        if($cart->user_id !== $request->user()->id || $item->cart_id !== $cart->id) {
            return response()->json([
                'ok' => false,
                'msg' => 'Você não tem permissão para realizar esta ação.'
            ], 403);            
        }

        $newQuantity = (int) $request->input('quantity');

        if ($newQuantity <= 0) {
            $item->delete();

            return response()->json([
                'ok' => true,
                'msg' => 'Item removido com sucesso.',
            ]);
        } else {
            $item->update(['quantity' => $newQuantity]);

            return response()->json([
                'ok' => true,
                'msg' => 'Item atualizado com sucesso.',
            ]);
        }
    }

    public function removeItem(Request $request, Cart $cart, CartItem $item)
    {
        if($cart->user_id !== $request->user()->id || $item->cart_id !== $cart->id) {
            return response()->json([
                'ok' => false,
                'msg' => 'Você não tem permissão para realizar esta ação.'
            ], 403);            
        }

        $item->delete();

        return response()->json([
            'ok' => true,
            'msg' => 'Item removido do carrinho.',
        ]);
    }
}
