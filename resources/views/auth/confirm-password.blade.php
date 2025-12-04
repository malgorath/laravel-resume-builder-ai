<x-guest-layout>
    <h2 class="text-center mb-4 fw-bold">Confirm Password</h2>
    <p class="text-center text-muted mb-4">
        This is a secure area of the application. Please confirm your password before continuing.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

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

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-lg">
                Confirm
            </button>
        </div>
    </form>
</x-guest-layout>
