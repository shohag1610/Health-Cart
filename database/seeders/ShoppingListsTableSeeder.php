<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShoppingList;
use App\Models\User;

class ShoppingListsTableSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('email', 'r@gmail.com')->first();

        if ($user) {
            ShoppingList::insert([
                [
                    'user_id' => $user->id,
                    'item_name' => 'Milk',
                    'item_price' => 3.50,
                    'is_purchased' => true,
                    'order' => 1,
                ],
                [
                    'user_id' => $user->id,
                    'item_name' => 'Bread',
                    'item_price' => 2.00,
                    'is_purchased' => true,
                    'order' => 2,
                ],
                [
                    'user_id' => $user->id,
                    'item_name' => 'Eggs',
                    'item_price' => 4.00,
                    'is_purchased' => false,
                    'order' => 3,
                ],
                [
                    'user_id' => $user->id,
                    'item_name' => 'Butter',
                    'item_price' => 5.00,
                    'is_purchased' => false,
                    'order' => 4,
                ],
            ]);
        }
    }
}
