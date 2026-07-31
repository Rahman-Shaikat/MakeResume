<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/common/media/favicon.png') }}">
    <title>{{ $title ?? 'Dashboard' }} - Resume Engineer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-page">
    <nav class="navbar navbar-expand-lg app-navbar sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <a href="{{ route('dashboard') }}" class="navbar-brand brand app-navbar-brand m-0" aria-label="Resume Engineer dashboard">
                <span class="brand-mark">
                    <img src="{{ asset('assets/common/media/logo.png') }}" width="42" height="42" alt="" aria-hidden="true">
                </span>
                Resume<span>Engineer</span>
            </a>

            <div class="d-flex align-items-center gap-3">
                <div class="nav-user d-none d-sm-flex">
                    <span class="nav-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <div>
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ auth()->user()->email }}</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-secondary btn-sm logout-button" type="submit">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 5H5v14h5M14 8l4 4-4 4m4-4H9"/></svg>
                        <span class="d-none d-sm-inline">Sign out</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    @if (session('status'))
        <div class="container-xl app-alert-container">
            <div
                class="alert alert-success alert-dismissible fade show app-alert"
                role="alert"
                data-auto-dismiss-alert
                data-dismiss-after="5000"
            >
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <x-site-footer />

    <div class="app-toast position-fixed bottom-0 end-0 p-3">
        <div class="toast align-items-center border-0 text-bg-dark" role="status" data-app-toast>
            <div class="d-flex">
                <div class="toast-body" data-toast-message></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
</body>
</html>
