<?php

namespace App\Actions\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CreateProductAction
{
    public function execute(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'barcode' => $data['barcode'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'min_stock' =>$data['min_stock'] ?? 5,
            ]);

            if (!empty($data['allergens'])) {
                $product->allergens()->sync($data['allergens']);
            }

            return $product->load(['category', 'allergens']);
        });
    }   
}