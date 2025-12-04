<x-guest-layout>
    <h2 class="text-center mb-4 fw-bold">Create Account</h2>
    <p class="text-center text-muted mb-4">Sign up to get started</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input 
                id="name" 
                class="form-control @error('name') is-invalid @enderror" 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                autofocus 
                autocomplete="name"
                placeholder="Enter your full name"
            />
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input 
                id="email" 
                class="form-control @error('email') is-invalid @enderror" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autocomplete="username"
                placeholder="Enter your email"
            />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input 
                id="password" 
                class="form-control @error('password') is-invalid @enderror"
                type="password"
                name="password"
                required 
                autocomplete="new-password"
                placeholder="Create a password"
            />
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input 
                id="password_confirmation" 
                class="form-control"
                type="password"
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                placeholder="Confirm your password"
            />
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-lg">
                Create Account
            </button>
        </div>

        <div class="auth-links">
            @if (Route::has('login'))
                <a href="{{ route('login') }}">
                    Already have an account? Sign in
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>
