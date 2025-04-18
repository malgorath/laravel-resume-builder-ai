<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Define the specific user details
        $email = 'test@home.net';
        $firstName = 'Test';
        $lastName = 'User';
        $defaultPassword = 'password';

        // Use firstOrCreate to prevent creating duplicates if the seeder runs again
        $user = User::firstOrCreate(
            [
                'email' => $email // Attribute to find the user by
            ],
            [
                'name' => $firstName . " " . $lastName,
                'password' => Hash::make($defaultPassword), // Hash the password
            ]
        );

        // Update the info message based on whether the user was created or found
        if ($user->wasRecentlyCreated) {
            $this->command->info("Created default user: {$firstName} {$lastName} ({$email}) with default password '{$defaultPassword}'.");
        } else {
            $this->command->info("Default user already exists: {$firstName} {$lastName} ({$email}). Password not changed.");
        }
        $numberOfUsers = 10; // How many users to create

        // Create users only
        User::factory($numberOfUsers)->create()->each(function ($user) {
            // You could add other user-specific seeding logic here if needed
        });

        // Update the info message
        $this->command->info("Seeded {$numberOfUsers} users.");
    }
}
