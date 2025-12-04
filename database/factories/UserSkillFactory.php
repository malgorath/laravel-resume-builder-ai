<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSkillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'skill_id' => Skill::factory(),
        ];
    }
}

