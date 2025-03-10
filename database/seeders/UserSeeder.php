<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['name' => 'Alice Johnson', 'email' => 'alice@example.com', 'password' => Hash::make('password')],
            ['name' => 'Bob Smith', 'email' => 'bob@example.com', 'password' => Hash::make('password')],
            ['name' => 'Charlie Davis', 'email' => 'charlie@example.com', 'password' => Hash::make('password')],
            ['name' => 'David Martinez', 'email' => 'david@example.com', 'password' => Hash::make('password')],
            ['name' => 'Emma Wilson', 'email' => 'emma@example.com', 'password' => Hash::make('password')],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
