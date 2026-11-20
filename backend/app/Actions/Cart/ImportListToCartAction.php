<?php

namespace App\Actions\Cart;

use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ShoppingList;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\DB;

class ImportListToCartAction {
    public function execute(User $user, ShoppingList $shoppingList){
        return DB::transaction(function () use ($user, $shoppingList) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'shopping_list_id' => $shoppingList->id,
                'access_token' => Str::random(20),
            ]);

            $shoppingList->load('items.product');

            foreach ($shoppingList->items as $item){
                if ($item->product) {
                    CartItem::create([
                        'user_id' => $user->id,
                        'cart_id' => $cart->id,
                        'product_id' => $item->product->id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->product->price
                    ]);
                }
            }

            return $cart;
        });
    }
}