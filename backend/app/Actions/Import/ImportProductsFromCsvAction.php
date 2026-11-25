<?php

namespace App\Actions\Import; 

use App\Services\BarcodeLookupService;
use App\Models\Product;
use Exception;

class ImportProductsFromCsvAction{
    public function __construct(private BarcodeLookupService $barcodeService) {
        
    }

    public function execute(string $filePath): array {
        if (!file_exists($filePath)) {
            throw new Exception("Error Processing Request", 1);
        }

        $handle = fopen($filePath, 'r');

        $header = fgetcsv($handle);

        $updated = 0;
        $failed = 0;
        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) { 
            $barcode = trim($row[0] ?? '');
            $price = (float) ($row[1] ?? 0.0);
            $minStock = (int) ($row[2] ?? 5);

            if ((!is_numeric($row[1] ?? null) || (float)$row[1] <= 0)) {
                $failed++;
                continue;
            }

            if ($barcode == ''){
                continue;
            }

            $data = $this->barcodeService->findByBarcode($barcode);

            if (!$data) {
                $failed++;
                continue;
            }

            if ($data['already_registered'] === true) {
                $data['product']->update(['price' => $price, 'min_stock' => $minStock]);
                $updated++;
            } else if ($data['already_registered'] === false) {
                 $product = Product::create([
                    'category_id' => $data['product']['category_id'],
                    'name'        => $data['product']['name'],
                    'barcode'     => $barcode,
                    'description' => $data['product']['description'] ?? null,
                    'price'       => $price,
                    'min_stock'   => $minStock,
                    'is_active'   => true,
                ]);

                if ($data['product']['allergens']) {
                    $product->allergens()->sync($data['product']['allergens']);
                }

                $imported++;
            }
         }

         fclose($handle);

        return [
            'imported' => $imported,
            'updated' => $updated,
            'failed' => $failed
        ];
    }
}