<?php

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateProductAction
{
    public function execute(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($data, $product) {

            $product->update($data);

            if (array_key_exists('allergens', $data)) {
                $product->allergens()->sync($data['allergens'] ?? []);
            }

            return $product;
        });
    }
}