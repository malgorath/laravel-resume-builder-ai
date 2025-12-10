<?php

use Database\Seeders\PromptSeeder;

test('prompt seeder seeds baseline prompts', function () {
    $this->seed(PromptSeeder::class);

    $this->assertDatabaseHas('prompts', ['key' => 'resume_analysis']);
    $this->assertDatabaseHas('prompts', ['key' => 'skill_extraction']);
    $this->assertDatabaseHas('prompts', ['key' => 'job_match']);
    $this->assertDatabaseHas('prompts', ['key' => 'cover_letter']);
});

