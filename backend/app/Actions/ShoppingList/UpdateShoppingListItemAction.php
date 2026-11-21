<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingListItem;


use Illuminate\Support\Facades\DB;

class UpdateShoppingListItemAction {
    public function execute(ShoppingListItem $item, array $data){
        return DB::transaction(function () use ($data, $item) {
            $item->update($data);

            return $item->refresh();
        });
    }
}