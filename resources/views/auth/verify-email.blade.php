<x-layouts.guest title="Verify your email - Resume Studio">
    <div class="verification-card">
        <div class="verification-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M4 6h16v12H4zM4 7l8 6 8-6"/><path d="m16.5 16.5 1.8 1.8 3.2-3.6"/></svg>
        </div>

        <div class="auth-heading verification-heading">
            <span class="auth-eyebrow">One quick step</span>
            <h2>Check your inbox</h2>
            <p>We sent a secure verification link to:</p>
        </div>

        <div class="verification-email">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4zm0 1 8 6 8-6"/></svg>
            <strong>{{ $email }}</strong>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div class="verification-success" role="status">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>
                <div>
                    <strong>Email sent successfully</strong>
                    <span>Open the latest message from Resume Studio to continue.</span>
                </div>
            </div>
        @endif

        <div class="verification-instructions">
            <div><span>1</span><p>Open the verification email we sent you.</p></div>
            <div><span>2</span><p>Click the <strong>Verify email address</strong> button.</p></div>
            <div><span>3</span><p>Return here and start building your resume.</p></div>
        </div>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-primary auth-submit w-100" type="submit">
                Resend verification email
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12a8 8 0 1 0 2.34-5.66L4 8.68M4 4v4.68h4.68"/></svg>
            </button>
        </form>

        <p class="verification-help">Can’t find it? Check your spam or promotions folder. The link expires after {{ config('auth.verification.expire', 60) }} minutes.</p>

        <form method="POST" action="{{ route('logout') }}" class="verification-logout">
            @csrf
            <span>Wrong email address?</span>
            <button type="submit">Sign out and register again</button>
        </form>
    </div>
</x-layouts.guest>
