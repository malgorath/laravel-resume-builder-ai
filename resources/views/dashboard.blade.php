@extends ('layouts.app')
@section ('content')
<a href="{{ route('resumes.index') }}" class="btn btn-primary">View Resumes ({!! $resumes->count() !!})</a>

@endsection
