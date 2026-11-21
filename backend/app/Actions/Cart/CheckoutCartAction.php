<?php

namespace App\Actions\Cart;

use Illuminate\Support\Facades\DB;
use App\Enums\CartStatus;
use App\Actions\Stock\RegisterStockExitAction;
use App\Models\User;
use App\Models\Cart;
use App\Models\StockBatch;
use Illuminate\Validation\ValidationException;

class CheckoutCartAction {
    public function __construct(private RegisterStockExitAction $stockExitAction) {

    }

    public function execute(User $user, Cart $cart): array{
        return DB::transaction(function () use ($user, $cart) {
           $cart->load(['items.product', 'shoppingList']);

           if ($cart->status !== CartStatus::open) {
                throw ValidationException::withMessages(['cart' => 'Este carrinho já foi finalizado ou cancelado.']);
           }

           if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'O carrinho está vazio.']);
           }

           $totalPrice = 0;

           foreach ($cart->items as $item) {
              $totalPrice += ($item->quantity * $item->unit_price);

              $batch = StockBatch::where('product_id', $item->product_id)->where('quantity', '>=', $item->quantity)->orderBy('expiration_date', 'asc')->first();

            if(!$batch) {
                throw ValidationException::withMessages(['stock' => "Estoque insuficiente para o produto {$item->product->name}."]);
            } else {
                $this->stockExitAction->execute($user, [ 'stock_batch_id' => $batch->id, 'quantity' => $item->quantity, 'unit_price' => $item->unit_price, 'reason' => 'Venda Beepo.', 'notes' => 'Carrinho #' . $cart->id ]);
            }
        }
        
        $cart->update(['status' => CartStatus::completed]);

           if ($cart->shopping_list_id) {
              $cart->shoppingList?->items()->whereIn('product_id', $cart->items->pluck('product_id'))->update(['is_checked' => true]);
           }

           return [
            'cart_id' => $cart->id,  
            'status' => $cart->status->value,
            'total_items' => $cart->items->sum('quantity'),
            'total_price' => $totalPrice,
            'items' => $cart->items,
            'completed_at' => now()->toIso8601String(),
           ];
        });
    }
}