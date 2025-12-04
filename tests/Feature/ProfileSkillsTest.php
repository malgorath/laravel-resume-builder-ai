<?php

use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserSkill;
use App\Models\Skill;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('user can view profile edit page', function () {
    $response = $this->get(route('profile.edit'));

    $response->assertStatus(200);
    $response->assertViewIs('profile.edit');
});

test('user can update profile details', function () {
    $response = $this->post(route('profile.details.update', $this->user->id), [
        'address' => '123 Main St',
        'phone' => '555-1234',
        'linkedin' => 'https://linkedin.com/in/test',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('user_details', [
        'user_id' => $this->user->id,
        'address' => '123 Main St',
        'phone' => '555-1234',
        'linkedin' => 'https://linkedin.com/in/test',
    ]);
});

test('user can add a skill', function () {
    $skill = Skill::factory()->create(['name' => 'PHP']);

    $response = $this->post(route('profile.skills.add', $this->user->id), [
        'skill' => 'PHP',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('user_skills', [
        'user_id' => $this->user->id,
        'skill_id' => $skill->id,
    ]);
});

test('user can remove a skill', function () {
    $skill = Skill::factory()->create();
    $userSkill = UserSkill::factory()->create([
        'user_id' => $this->user->id,
        'skill_id' => $skill->id,
    ]);

    $response = $this->delete(route('profile.skills.delete', $userSkill->id));

    $response->assertRedirect();
    $this->assertDatabaseMissing('user_skills', [
        'id' => $userSkill->id,
    ]);
});

test('user can confirm AI-detected skills', function () {
    $skill1 = Skill::factory()->create(['name' => 'JavaScript']);
    $skill2 = Skill::factory()->create(['name' => 'Python']);

    $response = $this->post(route('profile.skills.confirm'), [
        'skills' => ['JavaScript', 'Python'],
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('user_skills', [
        'user_id' => $this->user->id,
        'skill_id' => $skill1->id,
    ]);
    $this->assertDatabaseHas('user_skills', [
        'user_id' => $this->user->id,
        'skill_id' => $skill2->id,
    ]);
});

