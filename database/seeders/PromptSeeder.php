<?php

namespace Database\Seeders;

use App\Models\Prompt;
use Illuminate\Database\Seeder;

class PromptSeeder extends Seeder
{
    /**
     * Seed the prompts table with baseline prompts and Ollama configs.
     */
    public function run(): void
    {
        $defaultConfig = [
            'temperature' => 0.7,
            'top_p' => 0.9,
            'top_k' => 40,
            'repeat_penalty' => 1.1,
            'num_ctx' => 4096,
            'seed' => null,
            'max_tokens' => 800,
        ];

        $prompts = [
            [
                'key' => 'resume_analysis',
                'title' => 'Resume Analysis',
                'body' => "Analyze this resume:\n\n{{resume_text}}",
                'config' => $defaultConfig,
            ],
            [
                'key' => 'skill_extraction',
                'title' => 'Skill Extraction',
                'body' => "Extract technical and professional skill words from the following resume text and return as a comma separated array only, trimming off extra white spaces as needed, only the data no extra characters or text:\n\n{{resume_text}}",
                'config' => $defaultConfig,
            ],
            [
                'key' => 'job_skill_extraction',
                'title' => 'Job Skill Extraction',
                'body' => "Extract concise skill keywords from the following job description. Return a comma separated list of skill names only (no sentences, no extra text). Keep them lowercase and trim whitespace:\n\n{{job_description}}",
                'config' => $defaultConfig,
            ],
            [
                'key' => 'job_match',
                'title' => 'Job Matching',
                'body' => "Compare this resume with the job description and provide a match score (0-100) along with suggestions:\n\nResume:\n{{resume_text}}\n\nJob Description:\n{{job_description}}",
                'config' => $defaultConfig,
            ],
            [
                'key' => 'cover_letter',
                'title' => 'Cover Letter',
                'body' => "Write a professional cover letter for the following job application. Use the resume information provided to tailor the cover letter.\n\nApplicant Name: {{applicant_name}}\n\nResume Summary:\n{{resume_text}}\n\nJob Description:\n{{job_description}}\n\nWrite a compelling cover letter that highlights relevant experience and skills from the resume that match the job requirements.",
                'config' => $defaultConfig,
            ],
            [
                'key' => 'job_resume_comparison',
                'title' => 'Job vs Resume Comparison',
                'body' => "Compare the job description with the candidate resume and provide a concise report including: 1) match percentage 0-100, 2) key matching skills, 3) missing skills, 4) short recommendation. Keep it brief and bullet-like.\n\nJob Description:\n{{job_description}}\n\nResume:\n{{resume_text}}",
                'config' => $defaultConfig,
            ],
        ];

        foreach ($prompts as $prompt) {
            Prompt::updateOrCreate(
                ['key' => $prompt['key']],
                [
                    'title' => $prompt['title'],
                    'body' => $prompt['body'],
                    'config' => $prompt['config'],
                ]
            );
        }
    }
}

