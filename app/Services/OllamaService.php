<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Resume;

class OllamaService
{
    protected static $baseUrl;
    protected static $llm_model;

    public function __construct()
    {
        self::$baseUrl = env('OLLAMA_API_URL', 'http://localhost:11434/api/generate');
        self::$llm_model = env('OLLAMA_LLM_MODEL', 'gemma');
    }

    // Analyze resume with Ollama AI
    public function analyzeResume($resumeText, $resume)
    {
        $response = Http::timeout(120)->post(self::$baseUrl, [
            'model' => self::$llm_model,
            'prompt' => "Analyze this resume:\n\n" . $resumeText,
            'stream' => false,
        ]);

        $analysis = $response->json()['response'] ?? 'Error analyzing resume.';

        // Store the AI analysis with the resume
        $resume->ai_analysis = $analysis; // Store the analysis in the database
        $resume->save(); // Save the resume with the analysis


        return $analysis; // Return the AI analysis for frontend display
    }

    // Extract skills from resume text
    public static function extractSkills($text)
    {
        // Prepare the API request payload
        $response = Http::timeout(120)->post(self::$baseUrl, [
            'model' => self::$llm_model,
            'prompt' => "Extract technical and professional skill words from the following resume text: \n\n" . $text,
            'stream' => false
        ]);

        // Decode response
        if ($response->successful()) {
            $data = $response->json();
            return isset($data['response']) ? explode(",", $data['response']) : [];
        }

        return [];
    }

    public function matchJob($resumeText, $jobDescription)
    {
        $prompt = "Compare this resume with the job description and provide a match score (0-100) along with suggestions:\n\nResume:\n$resumeText\n\nJob Description:\n$jobDescription";

        $response = Http::post($this->baseUrl, [
            'model' => 'mistral',
            'prompt' => $prompt,
            'stream' => false,
        ]);

        return $response->json()['response'] ?? 'Error matching resume to job.';
    }
}
