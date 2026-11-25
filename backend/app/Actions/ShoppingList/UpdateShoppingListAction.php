<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingList;

use Illuminate\Support\Facades\DB;

class UpdateShoppingListAction {
    public function execute(ShoppingList $shoppingList, array $data){
        return DB::transaction(function () use ($shoppingList, $data) {
            $shoppingList->update($data);

            return $shoppingList;
        });
    }
}