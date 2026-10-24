<?php

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ToggleProductAction{
    public function execute(Product $product): Product
    {
        return DB::transaction(function () use ($product) {
            $product->update(['is_active' => !$product->is_active]);

            return $product;
        });
    }
}