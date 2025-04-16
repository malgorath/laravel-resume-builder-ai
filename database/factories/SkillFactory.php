<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Skill>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // We'll primarily create skills directly in the seeder for a predefined list,
        // but this factory can be useful for other testing scenarios.
        return [
            'name' => $this->faker->unique()->word(), // Example: Generate random word skill
        ];
    }
}
