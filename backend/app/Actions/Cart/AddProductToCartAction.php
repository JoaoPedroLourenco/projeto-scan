<?php

namespace App\Actions\Cart;

use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ShoppingListItem;
use App\Enums\CartStatus;
use Illuminate\Support\Facades\DB;

class AddProductToCartAction {
    public function execute(User $user, Cart $cart, Product $product, int $quantity, bool $ignoreAllergens = false){
        return DB::transaction(function () use ($user, $cart, $product, $quantity, $ignoreAllergens) {
            if ($cart->status !== CartStatus::open) {
                throw \Illuminate\Validation\ValidationException::withMessages(['cart' => 'Não é possível adicionar itens a um carrinho já finalizado ou cancelado.']); 
            }

            if (!$product->is_active) {
                throw \Illuminate\Validation\ValidationException::withMessages(['product_id' => 'Este produto está inativo e não pode ser adicionado ao carrinho.']); 
            }

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

            $freshCartItem = $cartItem->fresh();

            if($cart->shopping_list_id) {

                $listItem  = ShoppingListItem::where('shopping_list_id', $cart->shopping_list_id)
                                            ->where('product_id', $product->id)
                                            ->first();
                
                if($listItem && $freshCartItem->quantity >= $listItem->quantity) {
                    $listItem->update(['is_checked' => true]);
                }
            }

            return $freshCartItem;
        });
    }
}