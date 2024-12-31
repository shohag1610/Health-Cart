<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Rana',
            'email' => 'r@gmail.com',
            'total_budget' => 100.00,
            'password' => Hash::make('12345678'), // Use bcrypt to hash passwords
        ]);
    }
}
