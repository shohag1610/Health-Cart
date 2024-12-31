<?php

namespace App\Http\Controllers;
use App\Models\ShoppingList;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\User;


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

    // Update Purchased Status
    public function updatePurchaseStatus(Request $request)
    {
        try {
            $item = ShoppingList::where('item_name', $request->item_name)
                ->where('user_id', auth()->id())
                ->first();

            if ($item) {
                $item->is_purchased = $request->is_purchased;
                $item->save();
            }

            return response()->json(['message' => 'Item added to checked list', 'success' => true]);

        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Purchase Status'
            ]);
        }
    }

    // Delete Item
    public function destroyItem(Request $request)
    {
        try {
            $itemName = $request->input('item_name');
            $userId = auth()->id();

            $item = ShoppingList::where('item_name', $itemName)->where('user_id', $userId)->first();

            if (!$item) {
                return response()->json(['success' => false, 'message' => 'Item not found'], 404);
            }

            $itemPrice = $item->item_price;
            $user = User::find($userId);
            if ($item->is_purchased) {
                $user->total_budget -= $itemPrice;
                $user->save();
            }

            $item->delete();
            return response()->json(['message' => 'Item Deleted', 'success' => true, 'new_budget' => $user->total_budget]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete the item.'
            ]);
        }
    }

}
