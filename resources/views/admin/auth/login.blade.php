<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/common/media/favicon.png') }}">
    <title>Administrator sign in - Resume Engineer</title>
    @vite(['resources/css/admin/app.css', 'resources/js/admin/app.js'])
</head>
<body class="admin-body admin-auth-body">
    <main class="admin-auth-shell">
        <section class="admin-auth-card" aria-labelledby="admin-login-title">
            <div class="admin-auth-brand">
                <span class="admin-brand-mark">
                    <img src="{{ asset('assets/common/media/logo.png') }}" alt="">
                </span>
                <span>
                    <strong>Resume<span>Engineer</span></strong>
                    <small>Administration</small>
                </span>
            </div>

            <header>
                <span class="admin-eyebrow">Protected area</span>
                <h1 id="admin-login-title">Welcome back</h1>
                <p>Sign in with your administrator credentials.</p>
            </header>

            @include('admin.partials.alerts')

            <form action="{{ route('admin.login') }}" method="POST" class="admin-form">
                @csrf
                <x-admin.forms.input
                    name="email"
                    label="Email address"
                    type="email"
                    :value="old('email')"
                    autocomplete="username"
                    autofocus
                    required
                />
                <x-admin.forms.input
                    name="password"
                    label="Password"
                    type="password"
                    autocomplete="current-password"
                    required
                />
                <button type="submit" class="btn btn-primary w-100">Sign in to administration</button>
            </form>

            <footer>
                <a href="{{ route('home') }}">Return to Resume Engineer</a>
            </footer>
        </section>
    </main>
</body>
</html>
