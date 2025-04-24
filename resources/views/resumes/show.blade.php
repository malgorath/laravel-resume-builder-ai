@extends('layouts.app')

@section('content')
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Client Details</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Name:</strong> {{ $resume->user->name }}<br>
                    <strong>Email:</strong> {{ $resume->user->email }}<br>
                    <strong>Role:</strong> {{ $resume->user->role }}<br>
                    <strong>Created At:</strong> {{ $resume->user->created_at->format('d/m/Y H:i') }}<br>
                </div>
            </div>
    </div>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">AI Resume Details</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Filename:</strong> {{ $resume->filename }}<br>
                    <strong>Uploaded At:</strong> {{ $resume->created_at->format('d/m/Y H:i') }}<br>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    @markdown($resume->ai_analysis)
                </div>
            </div>
    </div>
    <p></p>
    <a href="{{ route('resumes.download', $resume->id) }}" class="btn btn-sm btn-warning">Download {{ $resume->filename }}</a>
    <a href="{{ route('resumes.index') }}" class="btn btn-success btn-sm">Back to Resumes List</a>
</div>
@endsection
