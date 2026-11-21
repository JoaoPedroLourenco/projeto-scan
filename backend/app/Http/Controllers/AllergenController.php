<?php

namespace App\Http\Controllers;

use App\Models\Allergen;
use Illuminate\Http\JsonResponse;

class AllergenController extends Controller
{
    public function index(): JsonResponse
    {
        $allergens = Allergen::where('is_active', true)->get();

        return response()->json([
            'ok' => true,
            'allergens' => $allergens
        ]);
    }
}
