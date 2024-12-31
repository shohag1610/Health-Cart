<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Mail\ShoppingListMail;
use Illuminate\Support\Facades\Mail;


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

    //Update Budget
    public function updateBudget(Request $request)
    {
        $validated = $request->validate([
            'budget' => 'required|numeric|min:0',
        ]);

        try {
            $user = Auth::user();
            $user->total_budget = $validated['budget'];
            $user->save();

            return response()->json(['message' => 'Budget Updated', 'success' => true]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update budget.'
            ]);
        }
    }

    //Email Shopping List
    public function sendShoppingListByEmail(Request $request)
    {
        try {
            $user = Auth::user();
            $emailAddress = $request->input('emailAddress');
            $uncheckedItems = ShoppingList::where('user_id', $user->id)
                ->where('is_purchased', false)
                ->get();

            $checkedItems = ShoppingList::where('user_id', $user->id)
                ->where('is_purchased', true)
                ->get();

            Mail::to($emailAddress)
                ->send(new ShoppingListMail($uncheckedItems, $checkedItems, $user));

            return response()->json(['success' => true, 'message' => 'Shopping list sent to your partner!']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send shopping list via email. '
            ]);
        }
    }

}
