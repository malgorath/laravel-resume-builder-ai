<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Company;
use App\Services\JobSkillService;
use App\Services\JobMatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function __construct(private JobSkillService $jobSkillService, private JobMatchService $jobMatchService)
    {
        // Only admins can create, edit, update, or delete jobs
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of jobs.
     */
    public function index(Request $request)
    {
        $query = Job::with('listingSkills');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate(15);

        if (auth()->check()) {
            $user = auth()->user()->load('skills');
            foreach ($jobs as $job) {
                $job->match_percent = $this->jobMatchService->calculateMatch($job, $user);
            }
        }
        
        return view('jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new job.
     */
    public function create()
    {
        $companies = Company::all();
        return view('jobs.create', compact('companies'));
    }

    /**
     * Store a newly created job.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'requirements' => 'nullable|string',
        ]);

        // Convert requirements string to array if provided
        if (!empty($validated['requirements'])) {
            $validated['requirements'] = array_filter(
                array_map('trim', explode("\n", $validated['requirements']))
            );
        } else {
            $validated['requirements'] = null;
        }

        $job = Job::create($validated);

        // Extract job skills on create
        $this->jobSkillService->extractAndAttach($job);

        return redirect()->route('jobs.index')->with('success', 'Job listing created successfully.');
    }

    /**
     * Display the specified job.
     */
    public function show(Job $job)
    {
        $job->load('listingSkills');

        // If no skills yet, attempt extraction on first view
        $this->jobSkillService->extractAndAttach($job);
        $job->load('listingSkills');

        $userApplications = Auth::check() 
            ? Auth::user()->applications()->where('job_id', $job->id)->get()
            : collect();
        
        return view('jobs.show', compact('job', 'userApplications'));
    }

    /**
     * Compare job vs user resume and return report JSON.
     */
    public function compareResume(Job $job)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $job->load('listingSkills');

        $user = Auth::user()->load('resumes');
        $resume = $this->resolveResume($user);
        if (!$resume) {
            return response()->json(['message' => 'No resume available'], 422);
        }

        $resumeText = $resume->ai_analysis ?? $resume->filename;

        $ollama = app(\App\Services\OllamaService::class);

        [$prompt, $config] = $ollama->promptPayload(
            'job_resume_comparison',
            [
                'job_description' => $job->description,
                'resume_text' => $resumeText,
            ],
            "Compare the job description with the candidate resume and provide a concise report including: 1) match percentage 0-100, 2) key matching skills, 3) missing skills, 4) short recommendation. Keep it brief and bullet-like.\n\nJob Description:\n{{job_description}}\n\nResume:\n{{resume_text}}"
        );

        $response = $ollama->postToOllama($prompt, $config);
        if (!$response->successful()) {
            return response()->json(['message' => 'Comparison failed'], 500);
        }

        $report = $response->json()['response'] ?? 'No report generated.';

        return response()->json([
            'report' => $report,
            'resume_used' => $resume->filename,
        ]);
    }

    private function resolveResume($user)
    {
        return $user->resumes()
            ->orderByDesc('is_primary')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Show the form for editing the specified job.
     */
    public function edit($id)
    {
        $job = Job::findOrFail($id);
        $companies = Company::all();
        return view('jobs.edit', compact('job', 'companies'));
    }

    /**
     * Update the specified job.
     */
    public function update(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'requirements' => 'nullable|string',
        ]);

        // Convert requirements string to array if provided
        if (!empty($validated['requirements'])) {
            $validated['requirements'] = array_filter(
                array_map('trim', explode("\n", $validated['requirements']))
            );
        } else {
            $validated['requirements'] = null;
        }

        $job->update($validated);

        return redirect()->route('jobs.show', $job->id)->with('success', 'Job listing updated successfully.');
    }

    /**
     * Remove the specified job.
     */
    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();

        return redirect()->route('jobs.index')->with('success', 'Job listing deleted successfully.');
    }
}
