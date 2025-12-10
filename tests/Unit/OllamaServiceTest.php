<?php

use App\Models\Resume;
use App\Models\User;
use App\Services\OllamaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('stores fallback analysis when ollama is unreachable', function () {
    Http::fake(function () {
        throw new Exception('connection refused');
    });

    $user = User::factory()->create();
    $resume = Resume::create([
        'user_id' => $user->id,
        'filename' => 'resume.pdf',
        'mime_type' => 'application/pdf',
        'file_data' => 'dummy',
    ]);

    $service = app(OllamaService::class);
    $analysis = $service->analyzeResume('test resume text', $resume);

    expect($analysis)->toContain('AI analysis unavailable');
    expect($resume->fresh()->ai_analysis)->toContain('AI analysis unavailable');
});

