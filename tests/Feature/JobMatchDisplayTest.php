<?php

use App\Models\Job;
use App\Models\JobListingSkill;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('jobs index shows match percent for authenticated user', function () {
    $job = Job::factory()->create();
    $skillA = JobListingSkill::factory()->create(['name' => 'php']);
    $skillB = JobListingSkill::factory()->create(['name' => 'laravel']);
    $job->listingSkills()->attach([$skillA->id, $skillB->id]);

    // Give user one matching skill
    $userSkill = \App\Models\Skill::create(['name' => 'php']);
    \App\Models\UserSkill::create([
        'user_id' => $this->user->id,
        'skill_id' => $userSkill->id,
    ]);

    $response = $this->get(route('jobs.index'));

    $response->assertOk();
    $response->assertSee('Match: 50%', false);
});

