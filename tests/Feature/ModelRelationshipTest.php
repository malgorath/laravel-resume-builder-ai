<?php

use App\Models\User;
use App\Models\Resume;
use App\Models\UserDetail;
use App\Models\UserSkill;
use App\Models\Skill;
use App\Models\Job;
use App\Models\Application;
use App\Models\Company;

test('user has many resumes', function () {
    $user = User::factory()->create();
    Resume::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->resumes)->toHaveCount(3);
    expect($user->resumes->first())->toBeInstanceOf(Resume::class);
});

test('user has one user detail', function () {
    $user = User::factory()->create();
    UserDetail::factory()->create(['user_id' => $user->id]);

    expect($user->userDetail)->toBeInstanceOf(UserDetail::class);
});

test('user has many skills through user_skills', function () {
    $user = User::factory()->create();
    $skill1 = Skill::factory()->create();
    $skill2 = Skill::factory()->create();
    
    UserSkill::factory()->create([
        'user_id' => $user->id,
        'skill_id' => $skill1->id,
    ]);
    UserSkill::factory()->create([
        'user_id' => $user->id,
        'skill_id' => $skill2->id,
    ]);

    expect($user->userSkills)->toHaveCount(2);
});

test('resume belongs to user', function () {
    $user = User::factory()->create();
    $resume = Resume::factory()->create(['user_id' => $user->id]);

    expect($resume->user)->toBeInstanceOf(User::class);
    expect($resume->user->id)->toBe($user->id);
});

test('job belongs to company', function () {
    $company = Company::factory()->create();
    $job = Job::factory()->create(['company_id' => $company->id]);

    expect($job->companyRelation)->toBeInstanceOf(Company::class);
    expect($job->companyRelation->id)->toBe($company->id);
});

test('application belongs to user and job', function () {
    $user = User::factory()->create();
    $job = Job::factory()->create();
    $application = Application::factory()->create([
        'user_id' => $user->id,
        'job_id' => $job->id,
    ]);

    expect($application->user)->toBeInstanceOf(User::class);
    expect($application->user->id)->toBe($user->id);
    expect($application->job)->toBeInstanceOf(Job::class);
    expect($application->job->id)->toBe($job->id);
});

test('job has many applications', function () {
    $job = Job::factory()->create();
    Application::factory()->count(3)->create(['job_id' => $job->id]);

    expect($job->applications)->toHaveCount(3);
});

test('user has many applications', function () {
    $user = User::factory()->create();
    Application::factory()->count(2)->create(['user_id' => $user->id]);

    expect($user->applications)->toHaveCount(2);
});

