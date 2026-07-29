<x-layouts.guest title="Create account - Resume Studio">
    <div class="auth-heading">
        <span class="auth-eyebrow">Get started</span>
        <h2>Create your account</h2>
        <p>Your first professional resume is a few steps away.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="auth-form" novalidate>
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Full name</label>
            <div class="input-with-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Your full name" autocomplete="name" required autofocus>
            </div>
            @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <div class="input-with-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4zm0 1 8 6 8-6"/></svg>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" autocomplete="email" required>
            </div>
            @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <label for="password" class="form-label">Password</label>
                <div class="input-with-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="8+ characters" autocomplete="new-password" required>
                </div>
            </div>
            <div class="col-sm-6">
                <label for="password_confirmation" class="form-label">Confirm password</label>
                <div class="input-with-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 12 3 3 7-7"/><rect x="4" y="3" width="16" height="18" rx="2"/></svg>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Repeat password" autocomplete="new-password" required>
                </div>
            </div>
            @error('password') <div class="col-12 mt-1"><div class="invalid-feedback d-block">{{ $message }}</div></div> @enderror
        </div>

        <p class="password-hint">Use at least 8 characters with letters and numbers.</p>

        <button class="btn btn-primary auth-submit w-100" type="submit">
            Create my account
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
        </button>
    </form>

    <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
</x-layouts.guest>
