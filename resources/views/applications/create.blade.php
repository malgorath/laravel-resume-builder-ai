@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('jobs.index') }}" class="btn btn-secondary">← Back to Jobs</a>
</div>

<div class="card">
    <div class="card-header">
        <h2>Apply for Job</h2>
        @if($job)
            <p class="mb-0 text-muted">{{ $job->title }} at {{ $job->company }}</p>
        @endif
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('applications.store') }}">
            @csrf

            @if($job)
                <input type="hidden" name="job_id" value="{{ $job->id }}">
            @else
                <div class="mb-3">
                    <label for="job_id" class="form-label">Select Job</label>
                    <select class="form-control @error('job_id') is-invalid @enderror" id="job_id" name="job_id" required>
                        <option value="">Choose a job...</option>
                        @foreach(\App\Models\Job::all() as $jobOption)
                            <option value="{{ $jobOption->id }}" {{ old('job_id') == $jobOption->id ? 'selected' : '' }}>{{ $jobOption->title }} - {{ $jobOption->company }}</option>
                        @endforeach
                    </select>
                    @error('job_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <div class="mb-3">
                <label for="resume_id" class="form-label">Select Resume (Optional)</label>
                <select class="form-control @error('resume_id') is-invalid @enderror" id="resume_id" name="resume_id">
                    <option value="">No resume selected</option>
                    @foreach($resumes as $resume)
                        <option value="{{ $resume->id }}" {{ old('resume_id') == $resume->id ? 'selected' : '' }}>{{ $resume->filename }}</option>
                    @endforeach
                </select>
                @error('resume_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes (Optional)</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Submit Application</button>
        </form>
    </div>
</div>
@endsection

