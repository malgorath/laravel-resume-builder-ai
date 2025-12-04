@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('jobs.index') }}" class="btn btn-secondary">← Back to Jobs</a>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ $job->title }}</h2>
        <p class="mb-0 text-muted">{{ $job->company }} - {{ $job->location }}</p>
    </div>
    <div class="card-body">
        <h5>Description</h5>
        <p>{{ $job->description }}</p>

        @if($job->requirements)
            <h5>Requirements</h5>
            <ul>
                @foreach($job->requirements as $requirement)
                    <li>{{ $requirement }}</li>
                @endforeach
            </ul>
        @endif

        @auth
            <hr>
            <h5>Apply for this Job</h5>
            @if($userApplications->count() > 0)
                <div class="alert alert-info">
                    You have already applied for this job.
                    @foreach($userApplications as $application)
                        <p>Status: <span class="badge bg-{{ $application->status === 'accepted' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($application->status) }}</span></p>
                    @endforeach
                </div>
            @else
                <a href="{{ route('applications.create', ['job_id' => $job->id]) }}" class="btn btn-primary">Apply Now</a>
            @endif
        @else
            <p class="text-muted">Please <a href="{{ route('login') }}">login</a> to apply for this job.</p>
        @endauth

        @auth
            @if(auth()->user()->isAdmin())
                <hr>
                <h5>Admin Actions</h5>
                <a href="{{ route('jobs.edit', $job->id) }}" class="btn btn-warning">Edit Job</a>
                <form action="{{ route('jobs.destroy', $job->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Job</button>
                </form>
            @endif
        @endauth
    </div>
</div>
@endsection

