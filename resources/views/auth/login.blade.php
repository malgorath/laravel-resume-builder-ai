<x-guest-layout>
    <h2 class="text-center mb-4 fw-bold">Welcome Back</h2>
    <p class="text-center text-muted mb-4">Sign in to your account to continue</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                autofocus 
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
                autocomplete="current-password"
                placeholder="Enter your password"
            />
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input 
                id="remember_me" 
                type="checkbox" 
                class="form-check-input" 
                name="remember"
            />
            <label class="form-check-label" for="remember_me">
                Remember me
            </label>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-lg">
                Sign In
            </button>
        </div>

        <div class="auth-links">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}">
                    Don't have an account? Sign up
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>
