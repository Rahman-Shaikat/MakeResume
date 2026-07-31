<x-layouts.guest title="Sign in - Resume Engineer">
    <div class="auth-heading">
        <span class="auth-eyebrow">Welcome back</span>
        <h2>Sign in to your account</h2>
        <p>Continue building your professional resume.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success py-2 small" role="alert">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <div class="input-with-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4zm0 1 8 6 8-6"/></svg>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" autocomplete="email" required autofocus>
            </div>
            @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <label for="password" class="form-label">Password</label>
            </div>
            <div class="input-with-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password" autocomplete="current-password" required>
                <button type="button" class="password-toggle" data-password-toggle="#password" aria-label="Show password">
                    <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                </button>
            </div>
            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember" @checked(old('remember'))>
            <label class="form-check-label" for="remember">Keep me signed in</label>
        </div>

        <button class="btn btn-primary auth-submit w-100" type="submit">
            Sign in
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
        </button>
    </form>

    <p class="auth-switch">New to Resume Engineer? <a href="{{ route('register') }}">Create an account</a></p>
</x-layouts.guest>
