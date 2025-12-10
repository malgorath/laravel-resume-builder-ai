<?php

use App\Models\Job;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('compare uses primary resume and returns report', function () {
    $job = Job::factory()->create(['description' => 'PHP developer role']);

    // Two resumes, one primary
    Resume::create([
        'user_id' => $this->user->id,
        'filename' => 'old.pdf',
        'mime_type' => 'application/pdf',
        'file_data' => 'x',
        'is_primary' => false,
        'ai_analysis' => 'Old resume',
    ]);

    Resume::create([
        'user_id' => $this->user->id,
        'filename' => 'primary.pdf',
        'mime_type' => 'application/pdf',
        'file_data' => 'y',
        'is_primary' => true,
        'ai_analysis' => 'Primary resume content',
    ]);

    Http::fake([
        config('ollama.api_url') => Http::response([
            'response' => 'Match 80%, good fit',
        ], 200),
    ]);

    $response = $this->postJson(route('jobs.compareResume', $job->id));

    $response->assertOk();
    $response->assertJsonFragment(['resume_used' => 'primary.pdf']);
    $response->assertJsonFragment(['report' => 'Match 80%, good fit']);
});

