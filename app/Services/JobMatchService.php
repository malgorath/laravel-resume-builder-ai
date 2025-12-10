<?php

namespace App\Services;

use App\Models\Job;
use App\Models\User;

class JobMatchService
{
    /**
     * Calculate match percentage based on overlap of job listing skills and user skills.
     */
    public function calculateMatch(Job $job, User $user): ?int
    {
        $jobSkills = $job->listingSkills->pluck('name')->map(fn ($s) => mb_strtolower($s))->unique();
        if ($jobSkills->isEmpty()) {
            return null;
        }

        $userSkills = $user->skills->pluck('name')->map(fn ($s) => mb_strtolower($s))->unique();
        if ($userSkills->isEmpty()) {
            return 0;
        }

        $overlap = $jobSkills->intersect($userSkills)->count();
        $total = $jobSkills->count();

        return (int) round(($overlap / $total) * 100);
    }
}

