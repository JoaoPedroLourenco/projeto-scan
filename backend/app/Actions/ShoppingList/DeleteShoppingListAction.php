<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingList;

use Illuminate\Support\Facades\DB;

class DeleteShoppingListAction {
    public function execute(ShoppingList $shoppingList){
        return DB::transaction(function () use ($shoppingList) {
            return $shoppingList->delete();
        });
    }
}