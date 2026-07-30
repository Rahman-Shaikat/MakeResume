<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/common/media/favicon.png') }}">
    <title>@yield('title', 'Dashboard') - Resume Studio Admin</title>
    @vite(['resources/css/admin/app.css', 'resources/js/admin/app.js'])
</head>
<body class="admin-body">
    <div class="admin-overlay" data-admin-overlay></div>

    @include('admin.partials.topbar')
    @include('admin.partials.sidebar')

    <main class="admin-content" data-admin-content>
        <div class="container-fluid">
            @include('admin.partials.alerts')
            @yield('content')
            @include('admin.partials.footer')
        </div>
    </main>
</body>
</html>
