<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ShoppingList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ShoppingListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can add item to shopping list.
     */
    public function test_user_can_add_item_to_shopping_list()
    {
        $user = User::factory()->create();  // Create a user

        // Authenticate the user
        $this->actingAs($user);

        $data = [
            'item_name' => 'Milk',
            'item_price' => 1.50
        ];

        $response = $this->post(route('shopping-list.store'), $data);

        $response->assertStatus(200);  // Successful addition
        $response->assertJson(['message' => 'Item added', 'success' => true]);

        $this->assertDatabaseHas('shopping_lists', [
            'item_name' => 'Milk',
            'item_price' => 1.50,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test user can delete an item from shopping list.
     */
    public function test_user_can_delete_item_from_shopping_list()
    {
        $user = User::factory()->create();
        $item = ShoppingList::create([
            'user_id' => $user->id,
            'item_name' => 'Eggs',
            'item_price' => 3.00
        ]);

        // Authenticate the user
        $this->actingAs($user);

        $data = [
            'item_name' => 'Eggs'
        ];

        $response = $this->post(route('shopping-list.destroy'), $data);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Item Deleted', 'success' => true]);

        $this->assertDatabaseMissing('shopping_lists', [
            'item_name' => 'Eggs',
        ]);
    }

    /**
     * Test user can update their budget.
     */
    public function test_user_can_update_budget()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'budget' => 100.00
        ];

        $response = $this->post(route('shopping-list.update-budget'), $data);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Budget Updated', 'success' => true]);

        $user->refresh();
        $this->assertEquals(100.00, $user->total_budget);
    }

    /**
     * Test user can send shopping list by email.
     */
    public function test_user_can_send_shopping_list_by_email()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create some items in the shopping list
        ShoppingList::create([
            'user_id' => $user->id,
            'item_name' => 'Butter',
            'item_price' => 1.50
        ]);
        ShoppingList::create([
            'user_id' => $user->id,
            'item_name' => 'Cheese',
            'item_price' => 2.50
        ]);

        $data = [
            'emailAddress' => 's@gmail.com'
        ];

        // Mock Mail
        Mail::fake();

        $response = $this->post(route('shopping-list.send-by-email'), $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'message' => 'Shopping list sent to your partner!']);

        // Assert email was sent
        Mail::assertSent(\App\Mail\ShoppingListMail::class);
    }

    /**
     * Test user can update item order in shopping list.
     */
    public function test_user_can_update_item_order()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create items with initial order
        $item1 = ShoppingList::create([
            'user_id' => $user->id,
            'item_name' => 'Apple',
            'item_price' => 1.00,
            'order' => 1
        ]);
        $item2 = ShoppingList::create([
            'user_id' => $user->id,
            'item_name' => 'Banana',
            'item_price' => 0.75,
            'order' => 2
        ]);

        $data = [
            'order' => [
                ['item_name' => 'Banana', 'order' => 1],
                ['item_name' => 'Apple', 'order' => 2],
            ]
        ];

        $response = $this->post(route('shopping-list.store-item-updated-position'), $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify the order has been updated
        $item1->refresh();
        $item2->refresh();
        $this->assertEquals(2, $item1->order);
        $this->assertEquals(1, $item2->order);
    }
}
