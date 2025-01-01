<?php

// tests/Feature/AuthTest.php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the registration form is accessible
     */
    public function test_registration_form_is_displayed()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /**
     * Test that the login form is accessible
     */
    public function test_login_form_is_displayed()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * Test user registration with valid data
     */
    public function test_user_can_register_with_valid_data()
    {
        $data = [
            'name' => 'Shohag Mia',
            'email' => 's@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->post(route('register'), $data);

        // Assert user is created
        $this->assertDatabaseHas('users', [
            'name' => 'Shohag Mia',
            'email' => 's@gmail.com',
        ]);

        // Assert redirect to login page with success message
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Registration successful. Please log in.');
    }

    /**
     * Test user registration with invalid data
     */
    public function test_user_cannot_register_with_invalid_data()
    {
        // Invalid email (already taken)
        $user = User::create([
            'name' => 'Shohag Mia',
            'email' => 'existing@example.com',
            'password' => Hash::make('12345678'),
        ]);

        $data = [
            'name' => 'Rana',
            'email' => 'existing@example.com', // Duplicate email
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];

        $response = $this->post(route('register'), $data);

        // Assert validation errors
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test user login with valid credentials
     */
    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::create([
            'name' => 'Shohag Mia',
            'email' => 's@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        $data = [
            'email' => 's@gmail.com',
            'password' => '12345678',
        ];

        $response = $this->post(route('login'), $data);

        // Assert user is redirected to the dashboard
        $response->assertRedirect(route('dashboard'));

        // Assert the user is logged in
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test user logout
     */
    public function test_user_can_logout()
    {
        $user = User::create([
            'name' => 'Shohag Mia',
            'email' => 's@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        $this->actingAs($user);

        $response = $this->post(route('logout'));

        // Assert user is logged out
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}

