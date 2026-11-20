<?php

namespace App\Http\Controllers;

use App\Actions\Stock\RegisterStockEntryAction;
use App\Actions\Stock\RegisterStockExitAction;
use App\Http\Requests\StockEntryRequest;
use App\Http\Requests\StockExitRequest;
use App\Models\StockBatch;
use Illuminate\Http\JsonResponse;

class StockController extends Controller
{
    public function index(): JsonResponse
    {
        $stockBatches = StockBatch::with('product')->paginate();

        return response()->json($stockBatches);
    }

    public function entry(StockEntryRequest $request, RegisterStockEntryAction $action): JsonResponse
    {
        $batch = $action->execute($request->user(), $request->validated());

        return response()->json([
            'ok' => true,
            'msg' => 'Entrada cadastrada com sucesso',
            'batch' => $batch,
        ], 201);
    }

    public function exit(StockExitRequest $request, RegisterStockExitAction $action): JsonResponse
    {
        $batch = $action->execute($request->user(), $request->validated());

        return response()->json([
            'ok' => true,
            'msg' => 'Saida cadastrada com sucesso',
            'batch' => $batch,
        ]);
    }
}