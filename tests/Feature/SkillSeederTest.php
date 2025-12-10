<?php

use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Database\Seeders\SkillSeeder;
use Illuminate\Support\Facades\Artisan;

test('skill seeder refreshes skills without foreign key issues', function () {
    $user = User::factory()->create();
    $skill = Skill::factory()->create();
    UserSkill::create([
        'user_id' => $user->id,
        'skill_id' => $skill->id,
    ]);

    Artisan::call('db:seed', ['--class' => SkillSeeder::class]);

    expect(Skill::count())->toBeGreaterThan(0);
    expect(UserSkill::count())->toBe(0);
});

