<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingListItem;

use Illuminate\Support\Facades\DB;

class DeleteShoppingListItemAction {
    public function execute(ShoppingListItem $item){
        return DB::transaction(function () use ($item) {
            $item->delete();
        });
    }
}