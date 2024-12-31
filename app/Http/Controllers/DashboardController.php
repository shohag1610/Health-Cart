<?php

namespace App\Http\Controllers;
use App\Models\ShoppingList;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;


class DashboardController extends Controller
{

    // Display Shopping List
    public function index()
    {
        try {
            $shoppingList = ShoppingList::where('user_id', auth()->id())
                ->orderBy('order', 'asc')
                ->get();
            return view('dashboard', compact('shoppingList'));

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve shopping list.'
            ]);
        }
    }

    // Add Item to Shopping List
    public function storeItem(Request $request)
    {

        $request->validate([
            'item_name' => 'required|string',
            'item_price' => 'required|numeric',
        ]);

        try {
            $shoppingList = new ShoppingList();
            $shoppingList->user_id = auth()->id();
            $shoppingList->item_name = $request->item_name;
            $shoppingList->item_price = $request->item_price;
            $shoppingList->save();

            return response()->json(['message' => 'Item added', 'success' => true]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Item Already Exist.'
            ]);
        }
    }
}
