@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('applications.index') }}" class="btn btn-secondary">← Back to Applications</a>
</div>

<div class="card">
    <div class="card-header">
        <h2>Application Details</h2>
    </div>
    <div class="card-body">
        <h5>Job Information</h5>
        <p><strong>Title:</strong> {{ $application->job->title }}</p>
        <p><strong>Company:</strong> {{ $application->job->company }}</p>
        <p><strong>Location:</strong> {{ $application->job->location }}</p>

        <hr>

        <h5>Application Status</h5>
        <p>
            <span class="badge bg-{{ $application->status === 'accepted' ? 'success' : ($application->status === 'rejected' ? 'danger' : ($application->status === 'reviewed' ? 'info' : 'warning')) }}">
                {{ ucfirst($application->status) }}
            </span>
        </p>

        @if($application->resume)
            <p><strong>Resume Used:</strong> {{ $application->resume->filename }}</p>
        @endif

        @if($application->notes)
            <p><strong>Notes:</strong> {{ $application->notes }}</p>
        @endif

        <p><strong>Applied:</strong> {{ $application->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Last Updated:</strong> {{ $application->updated_at->format('d/m/Y H:i') }}</p>
    </div>
</div>
@endsection

