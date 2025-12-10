<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Resume;
use App\Models\Skill;
use App\Models\UserSkill;
use Illuminate\Support\Facades\Log; 

class OllamaService
{
    protected static $baseUrl;
    protected static $llm_model;

    public function __construct()
    {
        self::$baseUrl = config('ollama.api_url');
        self::$llm_model = config('ollama.llm_model');
    }

    // Analyze resume with Ollama AI
    public function analyzeResume($resumeText, $resume)
    {
        $fallback = 'AI analysis unavailable. Please try again later.';

        try {
            $response = Http::timeout(config('ollama.timeout', 120))->post(self::$baseUrl, [
                'model' => self::$llm_model,
                'prompt' => "Analyze this resume:\n\n" . $resumeText,
                'stream' => false,
            ]);
        } catch (\Throwable $e) {
            Log::error("AI analysis request failed for resume {$resume->id}: {$e->getMessage()}");
            $resume->ai_analysis = $fallback;
            $resume->save();
            return $fallback;
        }

        if (! $response->successful()) {
            Log::error("AI analysis HTTP error for resume {$resume->id}: status {$response->status()}");
            $resume->ai_analysis = $fallback;
            $resume->save();
            return $fallback;
        }
        
        $analysis = $response->json()['response'] ?? $fallback;
        $skills = self::extractSkills($resumeText); // Extract skills from the resume text
        Log::info($skills); // Log the skills 

        // Lets Check if the skills are in the Skills table
        $allSkillNames = Skill::pluck('name')->toArray(); // Get all skill names as array
        foreach ($skills as $skill) {
            $skill = trim($skill); // Clean up whitespace
            // See if $skill is in the allSkills array and add missing skills to the Skill table
            if (!empty($skill) && !in_array($skill, $allSkillNames)) {
                $newSkill = new Skill();
                $newSkill->name = $skill;
                $newSkill->save();

                // Track this skill so we don't try to add it again in this loop
                $allSkillNames[] = $skill;

                // Add the new skill to the UserSkill table if it doesn't already exist
                if (!UserSkill::where('skill_id', $newSkill->id)->where('user_id', $resume->user_id)->exists()) {
                    $newUserSkill = new UserSkill();
                    $newUserSkill->user_id = $resume->user_id;
                    $newUserSkill->skill_id = $newSkill->id;
                    $newUserSkill->save();
                }
            }
        }         

        // Store the AI analysis with the resume
        $resume->ai_analysis = $analysis; // Store the analysis in the database
        $resume->save(); // Save the resume with the analysis

        return $analysis; // Return the AI analysis for frontend display
    }

    // Extract skills from resume text
    public static function extractSkills($text)
    {
        // Initialize static properties if not already set
        if (!isset(self::$baseUrl)) {
            self::$baseUrl = config('ollama.api_url');
        }
        if (!isset(self::$llm_model)) {
            self::$llm_model = config('ollama.llm_model');
        }

        try {
            // Prepare the API request payload
            $response = Http::timeout(config('ollama.timeout', 120))->post(self::$baseUrl, [
                'model' => self::$llm_model,
                'prompt' => "Extract technical and professional skill words from the following resume text and return as comma seperated array only, trimming off extra white spaces as needed, only the data no extra characters or text: \n\n" . $text,
                'stream' => false
            ]);
        } catch (\Throwable $e) {
            Log::error("Skill extraction request failed: {$e->getMessage()}");
            return [];
        }

        // Decode response
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['response'])) {
                $skills = explode(",", $data['response']);
                return array_map('trim', $skills);
            }
            return [];
        }

        return [];
    }

    public function matchJob($resumeText, $jobDescription)
    {
        $prompt = "Compare this resume with the job description and provide a match score (0-100) along with suggestions:\n\nResume:\n$resumeText\n\nJob Description:\n$jobDescription";

        $response = Http::timeout(config('ollama.timeout', 120))->post(self::$baseUrl, [
            'model' => self::$llm_model,
            'prompt' => $prompt,
            'stream' => false,
        ]);

        return $response->json()['response'] ?? 'Error matching resume to job.';
    }

    /**
     * Generate a cover letter based on resume and job description
     */
    public function generateCoverLetter($resumeText, $jobDescription, $userName = 'Applicant')
    {
        $prompt = "Write a professional cover letter for the following job application. Use the resume information provided to tailor the cover letter.\n\n";
        $prompt .= "Applicant Name: $userName\n\n";
        $prompt .= "Resume Summary:\n$resumeText\n\n";
        $prompt .= "Job Description:\n$jobDescription\n\n";
        $prompt .= "Write a compelling cover letter that highlights relevant experience and skills from the resume that match the job requirements.";

        $response = Http::timeout(config('ollama.timeout', 120))->post(self::$baseUrl, [
            'model' => self::$llm_model,
            'prompt' => $prompt,
            'stream' => false,
        ]);

        return $response->json()['response'] ?? 'Error generating cover letter.';
    }
}
