<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Resume;

class OllamaService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('OLLAMA_API_URL', 'http://localhost:11434/api/generate');
    }

    public function analyzeResume($resumeText, $resume)
    {
        // $resumeText = substr($resumeText, 0, 1000); // Limit text to 1000 chars

        $response = Http::post('http://localhost:11434/api/generate', [
            'model' => 'mistral', // Change to gemma or llama3 if needed
            'prompt' => "Analyze this resume:\n\n" . $resumeText,
            'stream' => false,
        ]);

        $analysis = $response->json()['response'] ?? 'Error analyzing resume.';

        // Store the AI analysis with the resume
        $resume->ai_analysis = $analysis; // Store the analysis in the database
        $resume->save(); // Save the resume with the analysis


        return $analysis; // Return the AI analysis for frontend display
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
