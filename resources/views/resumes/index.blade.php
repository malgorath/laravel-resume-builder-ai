@extends('layouts.app')

@section('content')
    <h1>Your Resumes</h1>
    <table class="table table-striped table-bordered table-hover">
        <caption>List of resumes uploaded by you</caption>
        <a href="{{ route('resumes.upload') }}" class="btn btn-sm btn-primary">Upload Resume</a>
        <p></p>
        <thead>
            <tr>
                <th>Filename</th>
                <th>AI Analysis</th>
                <th>Upload Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resumes as $resume)
                <tr>
                    <td>{{ $resume->filename }}</td>
                    <td>{{ $resume->ai_analysis ? 'Completed' : 'Not Analyzed' }}</td>
                    <td>{{ $resume->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('resumes.show', $resume->id) }}">View</a> | <a href="{{ route('resumes.download', $resume->id) }}">Download</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
