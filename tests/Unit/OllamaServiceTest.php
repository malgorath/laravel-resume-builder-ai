<?php

use App\Models\Resume;
use App\Models\Prompt;
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

it('uses database prompt and config when available', function () {
    $user = User::factory()->create();
    $resume = Resume::create([
        'user_id' => $user->id,
        'filename' => 'resume.pdf',
        'mime_type' => 'application/pdf',
        'file_data' => 'dummy',
    ]);

    Prompt::create([
        'key' => 'resume_analysis',
        'title' => 'Custom',
        'body' => 'DB prompt: {{resume_text}}',
        'config' => [
            'temperature' => 0.2,
            'max_tokens' => 123,
        ],
    ]);

    $captured = null;
    Http::fake(function ($request) use (&$captured) {
        if ($captured === null) {
            $captured = $request->data();
        }
        return Http::response(['response' => 'ok'], 200);
    });

    $service = app(OllamaService::class);
    $service->analyzeResume('hello world', $resume);

    expect($captured['prompt'])->toContain('DB prompt: hello world');
    expect($captured['temperature'])->toBe(0.2);
    expect($captured['max_tokens'])->toBe(123);
});

