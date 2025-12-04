@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('jobs.index') }}" class="btn btn-secondary">← Back to Jobs</a>
</div>

<div class="card">
    <div class="card-header">
        <h2>Post New Job</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('jobs.store') }}">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Job Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="company" class="form-label">Company</label>
                <input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company" value="{{ old('company') }}" required>
                @error('company')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}" required>
                @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Post Job</button>
        </form>
    </div>
</div>
@endsection

