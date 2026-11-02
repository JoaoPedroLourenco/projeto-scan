<?php

namespace App\Actions\Stock;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\User;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class RegisterStockEntryAction {
    public function execute(User $user, array $data): StockBatch {
        return DB::transaction(function () use ($user, $data) {
            $stock = StockBatch::create([
                'product_id' => $data['product_id'],
                'batch_code'=> $data['batch_code'],
                'quantity' => $data['quantity'],
                'expiration_date' => $data['expiration_date'],
            ]);

            StockMovement::create([
                'stock_batch_id' => $stock->id,
                'user_id' => $user->id,
                'type' => StockMovementType::entry,
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'],
                'reason' => $data['reason'] ?? 'Entrada de estoque',
                'notes' => $data['notes'] ?? null,
            ]);

            return $stock;
        });
    }
}