<?php

namespace App\Http\Controllers;
use App\Models\ShoppingList;
use Illuminate\Database\QueryException;


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
}
