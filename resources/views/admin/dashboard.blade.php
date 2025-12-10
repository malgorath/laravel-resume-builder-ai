@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Admin Dashboard</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Total Users</h5>
                <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary mt-2">View All</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Total Jobs</h5>
                <h2 class="mb-0">{{ $stats['total_jobs'] }}</h2>
                <a href="{{ route('admin.jobs.index') }}" class="btn btn-sm btn-outline-primary mt-2">View All</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Total Applications</h5>
                <h2 class="mb-0">{{ $stats['total_applications'] }}</h2>
                <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-outline-primary mt-2">View All</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-muted">Total Resumes</h5>
                <h2 class="mb-0">{{ $stats['total_resumes'] }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Users</h5>
            </div>
            <div class="card-body">
                @if($stats['recent_users']->count() > 0)
                    <ul class="list-unstyled mb-0">
                        @foreach($stats['recent_users'] as $user)
                            <li class="mb-2">
                                <strong>{{ $user->name }}</strong><br>
                                <small class="text-muted">{{ $user->email }} ({{ $user->role }})</small>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">No users yet.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Jobs</h5>
            </div>
            <div class="card-body">
                @if($stats['recent_jobs']->count() > 0)
                    <ul class="list-unstyled mb-0">
                        @foreach($stats['recent_jobs'] as $job)
                            <li class="mb-2">
                                <strong>{{ $job->title }}</strong><br>
                                <small class="text-muted">{{ $job->company }} - {{ $job->location }}</small>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">No jobs yet.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Applications</h5>
            </div>
            <div class="card-body">
                @if($stats['recent_applications']->count() > 0)
                    <ul class="list-unstyled mb-0">
                        @foreach($stats['recent_applications'] as $application)
                            <li class="mb-2">
                                <strong>{{ $application->user->name }}</strong><br>
                                <small class="text-muted">Applied to: {{ $application->job->title ?? 'N/A' }}</small><br>
                                <span class="badge bg-{{ $application->status === 'accepted' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">No applications yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('jobs.create') }}" class="btn btn-primary me-2">Post New Job</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">Manage Users</a>
                <a href="{{ route('admin.jobs.index') }}" class="btn btn-secondary me-2">Manage Jobs</a>
                <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary me-2">Manage Applications</a>
                <a href="{{ route('admin.prompts.index') }}" class="btn btn-outline-info">Manage AI Prompts</a>
            </div>
        </div>
    </div>
</div>
@endsection

