<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Welcome') — {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.fonts')
    @stack('styles')
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background: linear-gradient(160deg, var(--brand-foam) 0%, #eef2f1 60%, #e7edea 100%);">
    <div class="container">
        @include('partials.toast')
        @yield('content')
    </div>

    <x-loader />
    @include('partials.scripts')
    @stack('scripts')
</body>
</html>
