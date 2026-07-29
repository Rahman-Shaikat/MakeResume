<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f5f7fb">
    <title>{{ $title ?? 'Resume Studio' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-showcase">
            <a href="{{ route('home') }}" class="brand brand-light">
                <span class="brand-mark">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3.5h7.5L19 8v12.5H7z"/><path d="M14.5 3.5V8H19M10 12h6M10 15.5h6"/></svg>
                </span>
                Resume<span>Studio</span>
            </a>
            <div class="showcase-copy">
                <span class="showcase-kicker">Build with confidence</span>
                <h1>A resume that gets your story right.</h1>
                <p>Choose a professional layout, add your details, and create a polished resume ready for your next opportunity.</p>
                <div class="showcase-points">
                    <span><i>✓</i> Pixel-precise templates</span>
                    <span><i>✓</i> Print and PDF ready</span>
                    <span><i>✓</i> Your data stays private</span>
                </div>
            </div>
            <p class="showcase-foot">Designed for ambitious professionals.</p>
        </section>

        <section class="auth-content">
            <div class="auth-card">
                {{ $slot }}
            </div>
        </section>
    </main>
</body>
</html>
