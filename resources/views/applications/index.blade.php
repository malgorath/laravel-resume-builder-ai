@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>My Applications</h1>
    <a href="{{ route('jobs.index') }}" class="btn btn-primary">Browse Jobs</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($applications->count() > 0)
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Company</th>
                <th>Status</th>
                <th>Applied Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $application)
                <tr>
                    <td>{{ $application->job->title }}</td>
                    <td>{{ $application->job->company }}</td>
                    <td>
                        <span class="badge bg-{{ $application->status === 'accepted' ? 'success' : ($application->status === 'rejected' ? 'danger' : ($application->status === 'reviewed' ? 'info' : 'warning')) }}">
                            {{ ucfirst($application->status) }}
                        </span>
                    </td>
                    <td>{{ $application->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('applications.show', $application->id) }}" class="btn btn-sm btn-primary">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $applications->links() }}
    </div>
@else
    <div class="alert alert-info">You haven't applied for any jobs yet. <a href="{{ route('jobs.index') }}">Browse jobs</a> to get started.</div>
@endif
@endsection

