@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('jobs.index') }}" class="btn btn-secondary">← Back to Jobs</a>
</div>

<div class="card">
    <div class="card-header">
        <h2>{{ $job->title }}</h2>
        <p class="mb-0 text-muted">{{ $job->company }} - {{ $job->location }}</p>
    </div>
    <div class="card-body">
        @if($job->listingSkills->isNotEmpty())
            <div class="mb-3">
                <h6 class="mb-2">Skills</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($job->listingSkills as $skill)
                        <span class="badge bg-info text-dark">{{ $skill->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <h5>Description</h5>
        <p>{{ $job->description }}</p>

        @if($job->requirements)
            <h5>Requirements</h5>
            <ul>
                @foreach($job->requirements as $requirement)
                    <li>{{ $requirement }}</li>
                @endforeach
            </ul>
        @endif

        @auth
            <hr>
            <h5>Compare with My Resume</h5>
            <p class="text-muted">Run an AI comparison between this job and your primary (or latest) resume.</p>
            <button class="btn btn-outline-info mb-3" id="compare-btn" data-route="{{ route('jobs.compareResume', $job->id) }}">
                Compare with My Resume
            </button>

            <hr>
            <h5>Apply for this Job</h5>
            @if($userApplications->count() > 0)
                <div class="alert alert-info">
                    You have already applied for this job.
                    @foreach($userApplications as $application)
                        <p>Status: <span class="badge bg-{{ $application->status === 'accepted' ? 'success' : ($application->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($application->status) }}</span></p>
                    @endforeach
                </div>
            @else
                <a href="{{ route('applications.create', ['job_id' => $job->id]) }}" class="btn btn-primary">Apply Now</a>
            @endif
        @else
            <p class="text-muted">Please <a href="{{ route('login') }}">login</a> to apply for this job.</p>
        @endauth

        @auth
            @if(auth()->user()->isAdmin())
                <hr>
                <h5>Admin Actions</h5>
                <a href="{{ route('jobs.edit', $job->id) }}" class="btn btn-warning">Edit Job</a>
                <form action="{{ route('jobs.destroy', $job->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Job</button>
                </form>
            @endif
        @endauth
    </div>
</div>

<!-- Compare Modal -->
<div class="modal fade" id="compareModal" tabindex="-1" aria-labelledby="compareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="compareModalLabel">Job vs Resume Comparison</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="compare-loading" class="d-none">Running comparison...</div>
                <div id="compare-error" class="alert alert-danger d-none"></div>
                <pre id="compare-report" class="bg-light p-3 rounded" style="white-space: pre-wrap;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const compareBtn = document.getElementById('compare-btn');
    if (!compareBtn) return;

    const modalEl = document.getElementById('compareModal');
    const modal = new bootstrap.Modal(modalEl);
    const loadingEl = document.getElementById('compare-loading');
    const errorEl = document.getElementById('compare-error');
    const reportEl = document.getElementById('compare-report');

    compareBtn.addEventListener('click', async () => {
        loadingEl.classList.remove('d-none');
        errorEl.classList.add('d-none');
        errorEl.textContent = '';
        reportEl.textContent = '';
        modal.show();

        try {
            const response = await fetch(compareBtn.dataset.route, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();
            loadingEl.classList.add('d-none');

            if (!response.ok) {
                errorEl.textContent = data.message || 'Comparison failed.';
                errorEl.classList.remove('d-none');
                return;
            }

            reportEl.textContent = data.report || 'No report generated.';
        } catch (e) {
            loadingEl.classList.add('d-none');
            errorEl.textContent = 'Error running comparison.';
            errorEl.classList.remove('d-none');
        }
    });
});
</script>
@endpush
@endsection

