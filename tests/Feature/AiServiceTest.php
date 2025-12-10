<?php

use App\Models\Resume;
use App\Models\User;
use App\Services\OllamaService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->resume = Resume::factory()->create(['user_id' => $this->user->id]);
    $this->aiService = new OllamaService();
});

test('resume analysis returns valid response', function () {
    Http::fake([
        config('ollama.api_url') => Http::response([
            'response' => 'This resume shows strong technical skills in software development.',
        ], 200),
    ]);

    $resumeText = 'John Doe, Software Engineer with 5 years of experience in PHP and Laravel.';
    $result = $this->aiService->analyzeResume($resumeText, $this->resume);

    expect($result)->toBeString();
    expect($result)->toContain('technical skills');
});

test('skill extraction parses correctly', function () {
    Http::fake([
        config('ollama.api_url') => Http::response([
            'response' => 'PHP, Laravel, JavaScript, MySQL',
        ], 200),
    ]);

    $resumeText = 'Experienced in PHP, Laravel, JavaScript, and MySQL.';
    $skills = OllamaService::extractSkills($resumeText);

    expect($skills)->toBeArray();
    expect($skills)->toContain('PHP');
    expect($skills)->toContain('Laravel');
});

test('handles ollama connection timeout', function () {
    Http::fake([
        config('ollama.api_url') => Http::response([], 500),
    ]);

    $resumeText = 'Test resume content.';
    $result = $this->aiService->analyzeResume($resumeText, $this->resume);

    expect($result)->toContain('AI analysis unavailable');
});

test('handles malformed ollama response', function () {
    Http::fake([
        config('ollama.api_url') => Http::response([
            'error' => 'Invalid response',
        ], 200),
    ]);

    $resumeText = 'Test resume content.';
    $result = $this->aiService->analyzeResume($resumeText, $this->resume);

    expect($result)->toContain('AI analysis unavailable');
});

test('job matching returns score', function () {
    Http::fake([
        config('ollama.api_url') => Http::response([
            'response' => 'Match Score: 85/100. Strong alignment with required skills.',
        ], 200),
    ]);

    $resumeText = 'Software Engineer with PHP and Laravel experience.';
    $jobDescription = 'Looking for a PHP Laravel developer.';
    $result = $this->aiService->matchJob($resumeText, $jobDescription);

    expect($result)->toBeString();
    expect($result)->toContain('Match Score');
});

test('cover letter generation works', function () {
    Http::fake([
        config('ollama.api_url') => Http::response([
            'response' => 'Dear Hiring Manager, I am writing to express my interest...',
        ], 200),
    ]);

    $resumeText = 'Experienced software developer.';
    $jobDescription = 'Software Engineer position.';
    $result = $this->aiService->generateCoverLetter($resumeText, $jobDescription, 'John Doe');

    expect($result)->toBeString();
    expect($result)->toContain('Dear');
});

