<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingListItem;

use Illuminate\Support\Facades\DB;

class ToggleShoppingListItemAction  {
    public function execute(ShoppingListItem $shoppingListItem){
        return DB::transaction(function () use ($shoppingListItem) {
            $shoppingListItem->update(['is_checked' => !$shoppingListItem->is_checked]);

            return $shoppingListItem;
        });
    }
}