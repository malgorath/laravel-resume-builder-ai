<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * Display a listing of jobs.
     */
    public function index(Request $request)
    {
        $query = Job::query();

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
            'requirements' => 'nullable|array',
        ]);

        Job::create($validated);

        return redirect()->route('jobs.index')->with('success', 'Job listing created successfully.');
    }

    /**
     * Display the specified job.
     */
    public function show(Job $job)
    {
        $userApplications = Auth::check() 
            ? Auth::user()->applications()->where('job_id', $job->id)->get()
            : collect();
        
        return view('jobs.show', compact('job', 'userApplications'));
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
            'requirements' => 'nullable|array',
        ]);

        $job->update($validated);

        return redirect()->route('jobs.index')->with('success', 'Job listing updated successfully.');
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
