<x-guest-layout>
    <h2 class="text-center mb-4 fw-bold">Verify Your Email</h2>
    <p class="text-center text-muted mb-4">
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            A new verification link has been sent to the email address you provided during registration.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex flex-column gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    Resend Verification Email
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="d-grid">
                <button type="submit" class="btn btn-outline-secondary">
                    Log Out
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
