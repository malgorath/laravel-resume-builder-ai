<?php

use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Models\Resume;
use App\Models\Company;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->job = Job::factory()->create();
});

test('user can view job listings', function () {
    Job::factory()->count(5)->create();

    $response = $this->get(route('jobs.index'));

    $response->assertStatus(200);
    $response->assertViewIs('jobs.index');
});

test('user can search jobs', function () {
    Job::factory()->create(['title' => 'PHP Developer']);
    Job::factory()->create(['title' => 'JavaScript Developer']);

    $response = $this->get(route('jobs.index', ['search' => 'PHP']));

    $response->assertStatus(200);
    $response->assertSee('PHP Developer');
    $response->assertDontSee('JavaScript Developer');
});

test('user can view a specific job', function () {
    $response = $this->get(route('jobs.show', $this->job->id));

    $response->assertStatus(200);
    $response->assertViewIs('jobs.show');
    $response->assertViewHas('job');
});

test('user can create a job application', function () {
    $resume = Resume::factory()->create(['user_id' => $this->user->id]);

    $response = $this->post(route('applications.store'), [
        'job_id' => $this->job->id,
        'resume_id' => $resume->id,
        'notes' => 'I am very interested in this position.',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('applications', [
        'user_id' => $this->user->id,
        'job_id' => $this->job->id,
        'status' => 'pending',
    ]);
});

test('user can view their applications', function () {
    Application::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'job_id' => $this->job->id,
    ]);

    $response = $this->get(route('applications.index'));

    $response->assertStatus(200);
    $response->assertViewIs('applications.index');
    $this->assertCount(3, $response->viewData('applications'));
});

test('user can only see their own applications', function () {
    $otherUser = User::factory()->create();
    Application::factory()->create(['user_id' => $this->user->id]);
    Application::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->get(route('applications.index'));

    $response->assertStatus(200);
    $this->assertCount(1, $response->viewData('applications'));
});

test('user can view a specific application', function () {
    $application = Application::factory()->create([
        'user_id' => $this->user->id,
        'job_id' => $this->job->id,
    ]);

    $response = $this->get(route('applications.show', $application->id));

    $response->assertStatus(200);
    $response->assertViewIs('applications.show');
});

test('user cannot view another users application', function () {
    $otherUser = User::factory()->create();
    $application = Application::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->get(route('applications.show', $application->id));

    $response->assertStatus(403);
});

test('user can delete their application', function () {
    $application = Application::factory()->create([
        'user_id' => $this->user->id,
        'job_id' => $this->job->id,
    ]);

    $response = $this->delete(route('applications.destroy', $application->id));

    $response->assertRedirect();
    $this->assertDatabaseMissing('applications', ['id' => $application->id]);
});

