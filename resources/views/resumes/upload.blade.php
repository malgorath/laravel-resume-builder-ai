@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Upload Your Resume</h2>
    
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('resumes.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="resume" class="form-label">Upload Resume (PDF/DOCX)</label>
            <input type="file" class="form-control" name="resume" required>
        </div>
        <button type="submit" class="btn btn-sm btn-primary">Upload</button> <a href="{{ route('resumes.index') }}" class="btn btn-success btn-sm">Back to Resumes List</a>
    </form>
</div>
@endsection
