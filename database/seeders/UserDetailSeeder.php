<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserDetail;
use Faker\Factory as Faker;

class UserDetailSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (User::all() as $user) {
            UserDetail::create([
                'user_id' => $user->id,
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'linkedin' => 'https://linkedin.com/in/' . strtolower(str_replace(' ', '', $user->name)),
                'github' => 'https://github.com/' . strtolower(str_replace(' ', '', $user->name)),
                'website' => $faker->url,
            ]);
        }
    }
}
