<?php

namespace App\Actions\ShoppingList;

use App\Models\User;
use App\Models\ShoppingList;


use Illuminate\Support\Facades\DB;

class CreateShoppingListAction {
    public function execute(User $user, array $data){
        return DB::transaction(function () use ($user, $data) {
            $shoppingList = ShoppingList::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            return $shoppingList;
        });
    }
}