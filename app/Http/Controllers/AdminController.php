<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Models\Resume;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_jobs' => Job::count(),
            'total_applications' => Application::count(),
            'total_resumes' => Resume::count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_jobs' => Job::latest()->take(5)->get(),
            'recent_applications' => Application::latest()->take(5)->with(['user', 'job'])->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Display all users.
     */
    public function users()
    {
        $users = User::with(['resumes', 'applications'])->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Display all jobs.
     */
    public function jobs()
    {
        $jobs = Job::with(['companyRelation', 'applications'])->latest()->paginate(20);
        return view('admin.jobs.index', compact('jobs'));
    }

    /**
     * Display all applications.
     */
    public function applications()
    {
        $applications = Application::with(['user', 'job', 'resume'])->latest()->paginate(20);
        return view('admin.applications.index', compact('applications'));
    }
}
