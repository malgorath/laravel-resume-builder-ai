<x-guest-layout>
    <h2 class="text-center mb-4 fw-bold">Reset Password</h2>
    <p class="text-center text-muted mb-4">Enter your new password below</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input 
                id="email" 
                class="form-control @error('email') is-invalid @enderror" 
                type="email" 
                name="email" 
                value="{{ old('email', $request->email) }}" 
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
            <label for="password" class="form-label">New Password</label>
            <input 
                id="password" 
                class="form-control @error('password') is-invalid @enderror" 
                type="password" 
                name="password" 
                required 
                autocomplete="new-password"
                placeholder="Enter new password"
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
                placeholder="Confirm new password"
            />
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-lg">
                Reset Password
            </button>
        </div>

        <div class="auth-links">
            <a href="{{ route('login') }}">
                Back to login
            </a>
        </div>
    </form>
</x-guest-layout>
