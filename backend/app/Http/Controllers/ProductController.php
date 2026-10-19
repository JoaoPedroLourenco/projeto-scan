<?php

namespace App\Http\Controllers;

use App\Actions\Product\CreateProductAction;
use App\Actions\Product\ToggleProductAction;
use App\Actions\Product\UpdateProductAction;
use App\Actions\Product\DeleteProductAction;
use App\Filters\ProductFilter;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\BarcodeLookupService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(ProductFilter $filters): JsonResponse
    {
        $products = Product::filter($filters)->with(['category', 'allergens'])->paginate();

        return response()->json($products);
    }

    public function lookup(string $barcode, BarcodeLookupService $service): JsonResponse
    {
        $data = $service->findByBarcode($barcode);

        if (!$data) {
            return response()->json([
                'msg' => 'Produto não encontrado na base local nem externa.',
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

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'product' => $product->load(['category', 'allergens',])
        ]);
    }

    public function update(ProductRequest $request, Product $product, UpdateProductAction $action): JsonResponse
    {
        $validated = $request->validated();

        $product = $action->execute($product, $validated);

        return response()->json([
            'ok' => true,
            'product' => $product->load(['category', 'allergens'])
        ]);
    }

    public function toggleStatus(Product $product, ToggleProductAction $action): JsonResponse
    {
        $product = $action->execute($product);

        return response()->json([
            'ok' => true,
            'product' => $product
        ]);
    }

    public function destroy(Product $product, DeleteProductAction $action): JsonResponse
    {
        $product = $action->execute($product);

        return response()->json([
            'ok' => true,
            'msg' => 'Produto deletado'
        ]);
    }
}