<?php

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DeleteProductAction{
    public function execute(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            return $product->delete();
        });
    }
}