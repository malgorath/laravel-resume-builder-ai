<?php

use App\Models\Resume;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders loading overlay and data attribute on resumes list', function () {
    Resume::factory()->create([
        'user_id' => $this->user->id,
        'ai_analysis' => null,
    ]);

    $response = $this->get(route('resumes.index'));

    $response->assertOk();
    $response->assertSee('id="loading-overlay"', false);
    $response->assertSee('data-loading-overlay', false);
});

it('renders loading overlay on resume show page', function () {
    $resume = Resume::factory()->create([
        'user_id' => $this->user->id,
        'ai_analysis' => 'Sample analysis',
    ]);

    $response = $this->get(route('resumes.show', $resume->id));

    $response->assertOk();
    $response->assertSee('id="loading-overlay"', false);
});

