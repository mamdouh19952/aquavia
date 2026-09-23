<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    @include('partials.navbar')

    <main class="flex-grow-1 py-4">
        <div class="container">
            @include('partials.toast')

            @if ($errors->any())
                <x-alert type="danger">
                    <strong>Please check the following:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            @yield('content')
        </div>
    </main>

    @include('partials.footer')

    <x-loader />
    @include('partials.scripts')
    @stack('scripts')
</body>
</html>
