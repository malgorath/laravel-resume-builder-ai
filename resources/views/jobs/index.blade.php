@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Job Listings</h1>
    @auth
        @if(auth()->user()->isAdmin())
            <a href="{{ route('jobs.create') }}" class="btn btn-primary">Post New Job</a>
        @endif
    @endauth
</div>

<!-- Search Form -->
<form method="GET" action="{{ route('jobs.index') }}" class="mb-4">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Search jobs..." value="{{ request('search') }}">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
    </div>
</form>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($jobs->count() > 0)
    <div class="row">
        @foreach($jobs as $job)
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $job->title }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $job->company }} - {{ $job->location }}</h6>
                        <p class="card-text">{{ Str::limit($job->description, 150) }}</p>
                        <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-sm btn-primary">View Details</a>
                        @auth
                            <a href="{{ route('applications.create', ['job_id' => $job->id]) }}" class="btn btn-sm btn-success">Apply</a>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $jobs->links() }}
    </div>
@else
    <div class="alert alert-info">No job listings found.</div>
@endif
@endsection

