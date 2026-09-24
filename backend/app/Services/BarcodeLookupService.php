<?php

namespace App\Services;

use App\Models\Allergen;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BarcodeLookupService
{
    private array $translations = [
        'dairies'          => 'Laticínios',
        'breakfasts'       => 'Pequeno-almoço e Cereais',
        'beverages'        => 'Bebidas',
        'groceries'        => 'Mercearia',
        'chocolate-spreads'=> 'Cremes de Chocolate',
        
        'milk'             => 'Leite e derivados',
        'nuts'             => 'Frutos de casca rija',
        'soybeans'         => 'Soja',
        'gluten'           => 'Glúten',
        'eggs'             => 'Ovos',
    ];
    public function findByBarcode(string $barcode): ?array 
    {
        $response = Http::get("https://world.openfoodfacts.org/api/v2/product/{$barcode}.json");

        if ($response->failed() || !isset($response['product'])) {
            return null;
        }

        $product = $response['product'];

        $rawCategory = $product['categories_tags'][0] ?? 'geral';
        $cleanCategoryKey = Str::slug(Str::after($rawCategory, ':'));

        $categoryName = $this->translations[$cleanCategoryKey] ?? Str::headline($cleanCategoryKey);

        $category = Category::firstOrCreate(
            ['code' => $cleanCategoryKey],
            ['name' => $categoryName]
        );

        $allergenIds = [];
        $rawAllergens = $product['allergens_tags'] ?? [];

        foreach ($rawAllergens as $tag) {
            $allergenKey = Str::slug(Str::after($tag, ':'));
            
            $allergenName = $this->translations[$allergenKey] ?? Str::headline($allergenKey);

            $allergen = Allergen::firstOrCreate(
                ['name' => $allergenName]
            );

            $allergenIds[] = $allergen->id;
        }

        return [
            'barcode'     => $barcode,
            'name'        => $product['product_name'] ?? '',
            'description' => $product['generic_name'] ?? $product['ingredients_text'] ?? null,
            'category_id' => $category->id,
            'allergens'   => $allergenIds,
            'price'       => null,
            'min_stock'   => 5,
        ];
    }
}