<?php

namespace App\Actions\Cart;

use App\Models\User;
use App\Models\Cart;
use App\Models\ShoppingList;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\DB;

class ImportListToCartAction {
    public function execute(User $user, ShoppingList $shoppingList){
        return DB::transaction(function () use ($user, $shoppingList) {
            $shoppingList->items()->update(['is_checked' => false]);
            
            $cart = Cart::create([
                'user_id' => $user->id,
                'shopping_list_id' => $shoppingList->id,
                'access_token' => Str::random(20),
            ]);

            return $cart->load(['items.product', 'shoppingList.items.product']);
        });
    }
}