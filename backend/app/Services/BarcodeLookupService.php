<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BarcodeLookupService
{
    public function findByBarcode(string $barcode): ?array 
    {
        $response = Http::get("https://world.openfoodfacts.org/api/v2/product/{$barcode}.json");

        if ($response->failed() || !isset($response['product'])) {
            return null;
       }

       $product = $response['product'];

       return [
        'barcode' => $barcode,
        'name' => $product['product_name'] ?? null,
        'description' => $product['generic_name'] ?? null,
        'allergens' => $product['allergens_tags'] ?? [],     
       ];
    }
}