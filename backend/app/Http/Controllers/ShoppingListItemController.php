<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddShoppingListItemRequest;
use App\Http\Requests\UpdateShoppingListItemRequest;
use App\Actions\ShoppingList\AddItemToShoppingListAction;
use App\Actions\ShoppingList\UpdateShoppingListItemAction;
use App\Actions\ShoppingList\DeleteShoppingListItemAction;
use App\Models\ShoppingListItem;
use App\Models\ShoppingList;
use App\Models\Product;

class ShoppingListItemController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(AddShoppingListItemRequest $request, AddItemToShoppingListAction $action, ShoppingList $shoppingList)
    {
        $data = $request->validated();

        $product = Product::find($data['product_id']);

        if($shoppingList->user_id !== auth()->id()){
            return response()->json([
                'ok' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $shoppingItem = $action->execute($shoppingList, $product, $data['quantity'] ?? 1);

        return response()->json([
            'ok' => true,
            'message' => 'Item adicionado à lista de compras com sucesso',
            'shoppingItem' => $shoppingItem
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShoppingListItemRequest $request, UpdateShoppingListItemAction $action, ShoppingList $shoppingList, ShoppingListItem $item)
    {
        $data = $request->validated();

        if($item->shopping_list_id !== $shoppingList->id || $shoppingList->user_id !== auth()->id()){
            return response()->json([
                'ok' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $shoppingItem = $action->execute($item,$data);

        return response()->json([
            'ok' => true,
            'message' => 'Item atualizado na lista de compras',
            'shoppingItem' => $shoppingItem
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShoppingList $shoppingList, DeleteShoppingListItemAction $action, ShoppingListItem $item)
    {
        if($item->shopping_list_id !== $shoppingList->id || $shoppingList->user_id !== auth()->id()){
            return response()->json([
                'ok' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $shoppingItem = $action->execute($item);

        return response()->json([
            'ok' => true,
            'message' => 'Item removido da lista de compras',
            'shoppingItem' => $shoppingItem
        ], 200);
    }
}
