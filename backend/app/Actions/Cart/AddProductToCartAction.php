<?php

namespace App\Actions\Cart;

use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class AddProductToCartAction {
    public function execute(User $user, Cart $cart, Product $product, int $quantity, bool $ignoreAllergens = false){
        return DB::transaction(function () use ($user, $cart, $product, $quantity, $ignoreAllergens) {
            $userAllergensIds =  $user->allergens->pluck('id');
            $allergens =  $product->allergens->whereIn('id', $userAllergensIds);

            if ($allergens->isNotEmpty() && ! $ignoreAllergens) {
                throw \Illuminate\Validation\ValidationException::withMessages(['allergen' => 'Atenção! Este produto possui alérgenos incompatíveis com o seu perfil.']); 
            }

            $cartItem = $cart->items()->where('product_id', $product->id)->first();

            if ($cartItem) {
                $cartItem->increment('quantity', $quantity);
            } else {
                $cartItem = $cart->items()->create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                ]);
            }

            return $cartItem->fresh();
        });
    }
}