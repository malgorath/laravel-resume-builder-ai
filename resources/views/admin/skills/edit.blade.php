@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Edit Skill</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.skills.update', $skill) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $skill->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Skill</button>
            <a href="{{ route('admin.skills.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection

