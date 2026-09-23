<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.fonts')
    <style>
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .admin-sidebar {
            width: 250px;
            background-color: var(--brand-ink);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        .admin-sidebar a {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
        }
        .admin-sidebar a:hover,
        .admin-sidebar a.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.08);
        }
        .admin-sidebar .nav-link.active {
            border-inline-start: 3px solid var(--brand-aqua);
            background-color: rgba(23, 179, 190, .16);
        }
        .admin-content {
            margin-left: 250px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .admin-navbar {
            background-color: #10314f;
            color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .admin-main {
            flex: 1;
            background-color: var(--brand-foam);
            padding: 2rem;
        }
        .admin-footer {
            background-color: #10314f;
            color: white;
            padding: 1rem 2rem;
            text-align: center;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .admin-content {
                margin-left: 0;
            }
            .admin-main {
                padding: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="p-4 border-bottom border-secondary">
                <h5 class="mb-0" style="font-family: var(--font-display); font-weight: 600;">
                    <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none">
                        {{ config('app.name') }}
                    </a>
                </h5>
                <small class="text-muted">Admin Panel</small>
            </div>

            <nav class="p-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                           @class(['nav-link', 'active' => request()->routeIs('admin.dashboard')])>
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.work.index') }}"
                           @class(['nav-link', 'active' => request()->routeIs('admin.work.*')])>
                            <i class="bi bi-images"></i> Our Work
                        </a>
                    </li>
                </ul>

                <hr class="bg-secondary">

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}" class="nav-link">
                            <i class="bi bi-person"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" data-no-loader>
                            @csrf
                            <button type="submit" class="btn btn-link nav-link p-0 border-0" style="color: rgba(255, 255, 255, 0.8);">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="admin-content">
            <!-- Top Navbar -->
            <nav class="admin-navbar">
                <div class="container-fluid p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">@yield('page-title', 'Dashboard')</h6>
                        <span class="small">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </span>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="admin-main">
                @include('partials.toast')

                @if ($errors->any())
                    <x-alert type="danger">
                        <strong>Please check the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="admin-footer">
                <small>&copy; {{ date('Y') }} {{ config('app.name') }} Admin Panel. All rights reserved.</small>
            </footer>
        </div>
    </div>

    <x-loader />
    @include('partials.scripts')
    @stack('scripts')
</body>
</html>
