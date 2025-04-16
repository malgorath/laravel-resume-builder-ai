<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Resume;
use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $numberOfUsers = 10; // How many users to create
        $maxResumesPerUser = 4; // Max resumes per user (will be random between 1 and this)
        $maxSkillsPerResume = 15; // Max skills per resume (will be random between 5 and this)

        // Get all available skill IDs once for efficiency
        $skillIds = Skill::pluck('id')->toArray();

        if (empty($skillIds)) {
            $this->command->error('No skills found. Please run SkillSeeder first.');
            return;
        }

        User::factory($numberOfUsers)->create()->each(function ($user) use ($skillIds, $maxResumesPerUser, $maxSkillsPerResume) {
            $numberOfResumes = rand(1, $maxResumesPerUser);

            Resume::factory($numberOfResumes)
                ->for($user) // Associate resume with the current user
                ->create()
                ->each(function ($resume) use ($skillIds, $maxSkillsPerResume) {
                    // Select a random number of skills for this resume
                    $numberOfSkills = rand(5, $maxSkillsPerResume);

                    // Shuffle skill IDs and take a random subset
                    shuffle($skillIds);
                    $skillsToAttach = array_slice($skillIds, 0, $numberOfSkills);

                    // Attach the selected skills to the resume
                    $resume->skills()->attach($skillsToAttach);
                });
        });

         $this->command->info("Seeded {$numberOfUsers} users, each with random resumes and skills.");
    }
}
