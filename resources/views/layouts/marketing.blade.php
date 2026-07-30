<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/common/media/favicon.png') }}">
    <title>{{ $title ?? 'Resume Studio' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="marketing-page">
    <header class="marketing-header">
        <div class="container-xl marketing-nav">
            <a href="{{ route('home') }}" class="brand" aria-label="Resume Studio home">
                <span class="brand-mark">
                    <img src="{{ asset('assets/common/media/logo.png') }}" width="42" height="42" alt="" aria-hidden="true">
                </span>
                Resume<span>Studio</span>
            </a>

            <nav class="marketing-links" aria-label="Primary navigation">
                <a href="{{ route('home') }}#templates">Templates</a>
                <a href="{{ route('home') }}#how-it-works">How it works</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact') }}">Contact</a>
            </nav>

            <div class="marketing-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Open workspace</a>
                @else
                    <a href="{{ route('login') }}" class="marketing-login-link">Sign in</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Get started</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <x-site-footer />
</body>
</html>
