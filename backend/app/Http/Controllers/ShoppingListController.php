<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShoppingList;
use App\Actions\ShoppingList\CreateShoppingListAction;
use App\Actions\ShoppingList\UpdateShoppingListAction;
use App\Actions\ShoppingList\DeleteShoppingListAction;
use App\Http\Requests\StoreShoppingListRequest;
use App\Http\Requests\UpdateShoppingListRequest;

class ShoppingListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $shoppingLists = $user->shoppingLists;

        return response()->json([
            'ok' => true,
            'shoppingLists' => $shoppingLists
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShoppingListRequest $request, CreateShoppingListAction $action)
    {
        $user = $request->user();

        $data = $request->validated();

        $shoppingList = $action->execute($user, $data);

        return response()->json([
            'ok' => true,
            'shoppingList' => $shoppingList
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shoppingList = ShoppingList::findOrFail($id);

        if ($shoppingList->user_id === auth()->id()){
            $shoppingList->load('items.product');

            return response()->json([
                'ok' => true,
                'shoppingList' => $shoppingList
            ]);
        }

        return response()->json([
            'ok' => false,
            'message' => 'Lista de compras não encontrada'
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShoppingListRequest $request, UpdateShoppingListAction $action, string $id)
    {
        $shoppingList = ShoppingList::findOrFail($id);

        if ($shoppingList->user_id === auth()->id()) {
            $data = $request->validated();

            $shoppingList = $action->execute($shoppingList, $data);

            return response()->json([
                'ok' => true,
                'shoppingList' => $shoppingList
            ]);
        }

        return response()->json([
            'ok' => false,
            'message' => 'Lista de compras não encontrada'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, DeleteShoppingListAction $action)
    {
        $shoppingList = ShoppingList::findOrFail($id);

        if ($shoppingList->user_id === auth()->id()) {
            $shoppingList = $action->execute($shoppingList);

            return response()->json([
                'ok' => true,
                'message' => 'Lista de compras deletada com sucesso'
            ], 204);
        }  

        return response()->json([
            'ok' => false,
            'message' => 'Lista de compras não encontrada'
        ], 404);  
    }
}
