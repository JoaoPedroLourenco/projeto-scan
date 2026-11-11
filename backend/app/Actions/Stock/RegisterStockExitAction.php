<?php

namespace App\Actions\Stock;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\User;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class RegisterStockExitAction {
    public function execute(User $user, array $data): StockBatch {
        return DB::transaction(function () use ($user, $data) {
            $stockBatch = StockBatch::where('id', $data['stock_batch_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

            if ($data['quantity'] <= $stockBatch->quantity) {
                $stockBatch->decrement('quantity', $data['quantity']);
            } else {
                throw \Illuminate\Validation\ValidationException::withMessages(['quantity' => 'Estoque insuficiente para este lote.']);
            }

            StockMovement::create([
                'stock_batch_id' => $stockBatch->id,
                'user_id' => $user->id,
                'type' => StockMovementType::exit,
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'] ?? $stockBatch->product?->price ?? 0.00,
                'reason' => $data['reason'] ?? 'Saida de estoque',
                'notes' => $data['notes'] ?? null,
            ]);

            return $stockBatch->refresh();
        });
    }
}