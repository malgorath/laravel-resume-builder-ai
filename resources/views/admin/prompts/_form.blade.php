@php
    $prompt ??= null;
    $config = $prompt->config ?? [];
@endphp

<div class="mb-3">
    <label class="form-label">Key</label>
    <input type="text" name="key" class="form-control" value="{{ old('key', $prompt->key ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $prompt->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Prompt Body</label>
    <textarea name="body" rows="8" class="form-control" required>{{ old('body', $prompt->body ?? '') }}</textarea>
    <small class="text-muted">
        Use placeholders like
        @verbatim
            <code>{{resume_text}}</code>, <code>{{job_description}}</code>, <code>{{applicant_name}}</code>
        @endverbatim
    </small>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Temperature</label>
        <input type="number" step="0.01" name="temperature" class="form-control" value="{{ old('temperature', $config['temperature'] ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Top P</label>
        <input type="number" step="0.01" name="top_p" class="form-control" value="{{ old('top_p', $config['top_p'] ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Top K</label>
        <input type="number" step="1" name="top_k" class="form-control" value="{{ old('top_k', $config['top_k'] ?? '') }}">
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-4">
        <label class="form-label">Repeat Penalty</label>
        <input type="number" step="0.01" name="repeat_penalty" class="form-control" value="{{ old('repeat_penalty', $config['repeat_penalty'] ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Context Length (num_ctx)</label>
        <input type="number" step="1" name="num_ctx" class="form-control" value="{{ old('num_ctx', $config['num_ctx'] ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Max Tokens</label>
        <input type="number" step="1" name="max_tokens" class="form-control" value="{{ old('max_tokens', $config['max_tokens'] ?? '') }}">
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-4">
        <label class="form-label">Seed</label>
        <input type="number" step="1" name="seed" class="form-control" value="{{ old('seed', $config['seed'] ?? '') }}">
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary">Save Prompt</button>
    <a href="{{ route('admin.prompts.index') }}" class="btn btn-secondary">Cancel</a>
</div>

