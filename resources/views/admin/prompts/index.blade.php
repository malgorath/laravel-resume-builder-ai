@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Prompts</h1>
    <a href="{{ route('admin.prompts.create') }}" class="btn btn-primary">New Prompt</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Title</th>
                        <th>Updated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prompts as $prompt)
                        <tr>
                            <td class="text-monospace">{{ $prompt->key }}</td>
                            <td>{{ $prompt->title }}</td>
                            <td>{{ $prompt->updated_at->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.prompts.edit', $prompt) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form action="{{ route('admin.prompts.destroy', $prompt) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this prompt?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3">No prompts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($prompts->hasPages())
        <div class="card-footer">
            {{ $prompts->links() }}
        </div>
    @endif
</div>
@endsection

