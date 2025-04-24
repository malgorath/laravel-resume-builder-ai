{{-- /home/ssanders/Projects/laravel-resume-builder-ai/resources/views/dashboard.blade.php --}}
@extends ('layouts.app')

@section ('content')

{{-- Add a page header (since the layout's header slot was removed) --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Dashboard</h1>
    {{-- You could add other header elements here if needed --}}
</div>

{{-- Replace Tailwind card structure with Bootstrap card --}}
<div class="card shadow-sm">
    <div class="card-body">
        <p class="card-text mb-3">
            {{ __("You're logged in!") }} {{-- Added the standard dashboard message back --}}
        </p>
        {{-- Apply Bootstrap button classes to the link --}}
        <a href="{{ route('resumes.index') }}" class="btn btn-primary">
            View Resumes <span class="badge bg-secondary ms-1">{!! $resumes->count() !!}</span>
        </a>
    </div>
</div>

@endsection
