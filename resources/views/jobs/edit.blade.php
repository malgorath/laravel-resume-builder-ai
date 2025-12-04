@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-secondary">← Back to Job</a>
</div>

<div class="card">
    <div class="card-header">
        <h2>Edit Job Listing</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('jobs.update', $job->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Job Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $job->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="company" class="form-label">Company</label>
                <input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company" value="{{ old('company', $job->company) }}" required>
                @error('company')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $job->location) }}" required>
                @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $job->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="requirements" class="form-label">Requirements (one per line)</label>
                <textarea class="form-control @error('requirements') is-invalid @enderror" id="requirements" name="requirements" rows="5" placeholder="Enter requirements, one per line">{{ old('requirements', is_array($job->requirements) ? implode("\n", $job->requirements) : '') }}</textarea>
                <small class="form-text text-muted">Enter each requirement on a new line. They will be stored as an array.</small>
                @error('requirements')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Job</button>
            <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection

