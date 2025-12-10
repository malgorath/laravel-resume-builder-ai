<?php
namespace App\Services;

use App\Models\Prompt;
use App\Models\Resume;
use App\Models\Skill;
use App\Models\UserSkill;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    protected static $baseUrl;
    protected static $llm_model;
    protected array $defaultConfig = [
        'temperature' => 0.7,
        'top_p' => 0.9,
        'top_k' => 40,
        'repeat_penalty' => 1.1,
        'num_ctx' => 4096,
        'seed' => null,
        'max_tokens' => 800,
    ];

    public function __construct()
    {
        self::$baseUrl = config('ollama.api_url');
        self::$llm_model = config('ollama.llm_model');
    }

    // Analyze resume with Ollama AI
    public function analyzeResume($resumeText, $resume)
    {
        $fallback = 'AI analysis unavailable. Please try again later.';

        [$prompt, $config] = $this->promptPayload(
            'resume_analysis',
            ['resume_text' => $resumeText],
            "Analyze this resume:\n\n{{resume_text}}"
        );

        try {
            $response = $this->postToOllama($prompt, $config);
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
        $skills = $this->extractSkills($resumeText); // Extract skills from the resume text
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
    public function extractSkills($text)
    {
        // Initialize static properties if not already set
        if (!isset(self::$baseUrl)) {
            self::$baseUrl = config('ollama.api_url');
        }
        if (!isset(self::$llm_model)) {
            self::$llm_model = config('ollama.llm_model');
        }

        [$prompt, $config] = $this->promptPayload(
            'skill_extraction',
            ['resume_text' => $text],
            "Extract technical and professional skill words from the following resume text and return as comma seperated array only, trimming off extra white spaces as needed, only the data no extra characters or text: \n\n{{resume_text}}"
        );

        try {
            // Prepare the API request payload
            $response = $this->postToOllama($prompt, $config);
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
        [$prompt, $config] = $this->promptPayload(
            'job_match',
            [
                'resume_text' => $resumeText,
                'job_description' => $jobDescription,
            ],
            "Compare this resume with the job description and provide a match score (0-100) along with suggestions:\n\nResume:\n{{resume_text}}\n\nJob Description:\n{{job_description}}"
        );

        $response = $this->postToOllama($prompt, $config);

        return $response->json()['response'] ?? 'Error matching resume to job.';
    }

    /**
     * Generate a cover letter based on resume and job description
     */
    public function generateCoverLetter($resumeText, $jobDescription, $userName = 'Applicant')
    {
        [$prompt, $config] = $this->promptPayload(
            'cover_letter',
            [
                'resume_text' => $resumeText,
                'job_description' => $jobDescription,
                'applicant_name' => $userName,
            ],
            "Write a professional cover letter for the following job application. Use the resume information provided to tailor the cover letter.\n\nApplicant Name: {{applicant_name}}\n\nResume Summary:\n{{resume_text}}\n\nJob Description:\n{{job_description}}\n\nWrite a compelling cover letter that highlights relevant experience and skills from the resume that match the job requirements."
        );

        $response = $this->postToOllama($prompt, $config);

        return $response->json()['response'] ?? 'Error generating cover letter.';
    }

    /**
     * Build prompt text and config from DB or fallback template.
     */
    private function promptPayload(string $key, array $variables, string $fallbackTemplate): array
    {
        $prompt = Prompt::where('key', $key)->first();
        $template = $prompt?->body ?? $fallbackTemplate;
        $config = array_merge($this->defaultConfig, $prompt->config ?? []);

        return [$this->renderTemplate($template, $variables), $config];
    }

    private function renderTemplate(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }

    private function postToOllama(string $prompt, array $config)
    {
        $payload = array_merge([
            'model' => self::$llm_model,
            'prompt' => $prompt,
            'stream' => false,
        ], $this->filterConfig($config));

        return Http::timeout(config('ollama.timeout', 120))->post(self::$baseUrl, $payload);
    }

    private function filterConfig(array $config): array
    {
        return array_filter($config, fn ($value) => !is_null($value));
    }
}
