<?php

namespace App\Http\Controllers;

use App\Actions\Product\CreateProductAction;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\BarcodeLookupService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::with(['category', 'allergens'])->get();

        return response()->json($products);
    }

    public function lookup(string $barcode, BarcodeLookupService $service): JsonResponse
    {
        $data = $service->findByBarcode($barcode);

        if (!$data) {
            return response()->json([
                'msg' => 'Produto não encontrado externamente',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => $data
        ]);
    }

    public function store(ProductRequest $request, CreateProductAction $action): JsonResponse
    {
        $validated = $request->validated();

        $product = $action->execute($validated);

        return response()->json([
            'ok' => true,
            'product' => $product
        ], 201);
    }

    public function showByBarcode(string $barcode): JsonResponse
    {
        $product = Product::with(['category', 'allergens'])
            ->where('barcode', $barcode)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'ok' => true,
            'product' => $product
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'product' => $product->load(['category', 'allergens', 'stockBatches'])
        ]);
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();

        $product->update($validated);

        if (array_key_exists('allergens', $validated)) {
            $product->allergens()->sync($validated['allergens'] ?? []);
        }

        return response()->json([
            'ok' => true,
            'product' => $product->load(['category', 'allergens'])
        ]);
    }

    public function toggleStatus(Product $product): JsonResponse
    {
        $product->update(['is_active' => !$product->is_active]);

        return response()->json([
            'ok' => true,
            'is_active' => $product->is_active
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(null, 204);
    }
}