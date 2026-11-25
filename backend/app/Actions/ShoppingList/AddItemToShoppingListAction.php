<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingList;
use App\Models\Product;

use Illuminate\Support\Facades\DB;

class AddItemToShoppingListAction {
    public function execute(ShoppingList $shoppingList, Product $product, int $quantity){
        return DB::transaction(function () use ($shoppingList, $product, $quantity) {
            $shoppingItem = $shoppingList->items()->where('product_id', $product->id)->first();

            if ($shoppingItem) {
                $shoppingItem->increment('quantity', $quantity);
            } else {
                $shoppingItem = $shoppingList->items()->create([
                    'shopping_list_id' => $shoppingList->id,
                    'product_id' => $product->id,
                    'user_id' => $shoppingList->user_id,
                    'quantity' => $quantity,
                    'is_checked' => false,
                ]);
            }

            return $shoppingItem->fresh();
        });
    }
}