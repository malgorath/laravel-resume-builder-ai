<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Display a listing of applications for the authenticated user.
     */
    public function index()
    {
        $applications = Application::where('user_id', Auth::id())
            ->with(['job', 'resume'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new application.
     */
    public function create(Request $request)
    {
        $jobId = $request->get('job_id');
        $job = $jobId ? Job::findOrFail($jobId) : null;
        
        $resumes = Resume::where('user_id', Auth::id())->get();

        return view('applications.create', compact('job', 'resumes'));
    }

    /**
     * Store a newly created application.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:jobListings,id',
            'resume_id' => 'nullable|exists:resumes,id',
            'notes' => 'nullable|string',
        ]);

        // Ensure resume belongs to user
        if ($validated['resume_id']) {
            $resume = Resume::where('id', $validated['resume_id'])
                ->where('user_id', Auth::id())
                ->firstOrFail();
        }

        $application = Application::create([
            'user_id' => Auth::id(),
            'job_id' => $validated['job_id'],
            'resume_id' => $validated['resume_id'] ?? null,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('applications.index')
            ->with('success', 'Application submitted successfully.');
    }

    /**
     * Display the specified application.
     */
    public function show(Application $application)
    {
        // Ensure user owns this application
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $application->load(['job', 'resume', 'user']);

        return view('applications.show', compact('application'));
    }

    /**
     * Update the specified application.
     */
    public function update(Request $request, $id)
    {
        $application = Application::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,reviewed,accepted,rejected',
            'notes' => 'nullable|string',
        ]);

        $application->update($validated);

        return redirect()->route('applications.index')
            ->with('success', 'Application updated successfully.');
    }

    /**
     * Remove the specified application.
     */
    public function destroy($id)
    {
        $application = Application::where('user_id', Auth::id())->findOrFail($id);
        $application->delete();

        return redirect()->route('applications.index')
            ->with('success', 'Application deleted successfully.');
    }
}
