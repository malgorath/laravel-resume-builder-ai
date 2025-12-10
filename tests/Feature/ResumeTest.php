<?php

use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('user can view upload form', function () {
    $response = $this->get(route('resumes.upload.form'));

    $response->assertStatus(200);
    $response->assertViewIs('resumes.upload');
});

test('user can upload a PDF resume', function () {
    Storage::fake('local');
    
    $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

    $response = $this->post(route('resumes.upload'), [
        'resume' => [$file],
    ]);

    $response->assertRedirect();

    $resume = Resume::where('user_id', $this->user->id)->latest('id')->first();

    expect($resume)->not->toBeNull();
    expect($resume->filename)->toMatch('/^resume_\d+\.pdf$/');
    expect($resume->mime_type)->toBe('application/pdf');
});

test('user can upload a DOCX resume', function () {
    Storage::fake('local');
    
    $file = UploadedFile::fake()->create('resume.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

    $response = $this->post(route('resumes.upload'), [
        'resume' => [$file],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('resumes', [
        'user_id' => $this->user->id,
    ]);
});

test('user can upload multiple resumes', function () {
    Storage::fake('local');
    
    $file1 = UploadedFile::fake()->create('resume1.pdf', 100, 'application/pdf');
    $file2 = UploadedFile::fake()->create('resume2.pdf', 100, 'application/pdf');

    $response = $this->post(route('resumes.upload'), [
        'resume' => [$file1, $file2],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseCount('resumes', 2);
});

test('user cannot upload invalid file type', function () {
    $file = UploadedFile::fake()->create('document.txt', 100, 'text/plain');

    $response = $this->post(route('resumes.upload'), [
        'resume' => [$file],
    ]);

    $response->assertSessionHasErrors('resume.0');
});

test('user cannot upload oversized file', function () {
    $file = UploadedFile::fake()->create('large.pdf', 11000, 'application/pdf'); // 11MB

    $response = $this->post(route('resumes.upload'), [
        'resume' => [$file],
    ]);

    $response->assertSessionHasErrors('resume.0');
});

test('user can view their resumes list', function () {
    Resume::factory()->count(3)->create(['user_id' => $this->user->id]);

    $response = $this->get(route('resumes.index'));

    $response->assertStatus(200);
    $response->assertViewIs('resumes.index');
    $response->assertViewHas('resumes');
});

test('user can only see their own resumes', function () {
    $otherUser = User::factory()->create();
    Resume::factory()->create(['user_id' => $this->user->id]);
    Resume::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->get(route('resumes.index'));

    $response->assertStatus(200);
    $this->assertCount(1, $response->viewData('resumes'));
});

test('user can view a specific resume', function () {
    $resume = Resume::factory()->create(['user_id' => $this->user->id]);

    $response = $this->get(route('resumes.show', $resume->id));

    $response->assertStatus(200);
    $response->assertViewIs('resumes.show');
    $response->assertViewHas('resume');
});

test('user cannot view another users resume', function () {
    $otherUser = User::factory()->create();
    $resume = Resume::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->get(route('resumes.show', $resume->id));

    $response->assertStatus(403);
});

test('user can download their own resume', function () {
    $resume = Resume::factory()->create([
        'user_id' => $this->user->id,
        'file_data' => 'test file content',
        'mime_type' => 'application/pdf',
        'filename' => 'test_resume.pdf',
    ]);

    $response = $this->get(route('resumes.download', $resume->id));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('user cannot download another users resume', function () {
    $otherUser = User::factory()->create();
    $resume = Resume::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->get(route('resumes.download', $resume->id));

    $response->assertStatus(403);
});

test('user can reset AI analysis', function () {
    $resume = Resume::factory()->create([
        'user_id' => $this->user->id,
        'ai_analysis' => 'Some analysis',
    ]);

    $response = $this->get(route('resumes.resetAnalysis', $resume->id));

    $response->assertRedirect();
    $this->assertNull($resume->fresh()->ai_analysis);
});

