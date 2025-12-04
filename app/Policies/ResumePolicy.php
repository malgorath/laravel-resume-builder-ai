<?php

namespace App\Policies;

use App\Models\Resume;
use App\Models\User;

class ResumePolicy
{
    /**
     * Determine if the user can view the resume.
     */
    public function view(User $user, Resume $resume): bool
    {
        return $user->id === $resume->user_id;
    }

    /**
     * Determine if the user can update the resume.
     */
    public function update(User $user, Resume $resume): bool
    {
        return $user->id === $resume->user_id;
    }

    /**
     * Determine if the user can delete the resume.
     */
    public function delete(User $user, Resume $resume): bool
    {
        return $user->id === $resume->user_id;
    }

    /**
     * Determine if the user can download the resume.
     */
    public function download(User $user, Resume $resume): bool
    {
        return $user->id === $resume->user_id;
    }
}
