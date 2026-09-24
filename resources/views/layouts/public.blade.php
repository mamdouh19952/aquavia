<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>

    @if (app()->getLocale() === 'ar')
        @vite(['resources/css/app-rtl.css', 'resources/js/app.js'])
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @include('partials.fonts')
    @stack('styles')
</head>
<body class="bg-light">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">{{ config('app.name') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarPublic">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">{{ __('site.nav.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('about') }}">{{ __('site.nav.about') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('services') }}">{{ __('site.nav.services') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('work.index') }}">{{ __('site.nav.our_work') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact') }}">{{ __('site.nav.contact') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}">
                            {{ __('site.nav.switch_language') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container py-5">
            @include('partials.toast')
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="mb-2" style="font-family: var(--font-display); font-weight: 600; font-size: 1.4rem;">{{ config('app.name') }}</h5>
                    <p class="mb-3" style="color: rgba(255, 255, 255, .7); max-width: 320px;">{{ __('site.tagline') }}</p>
                    <div class="d-flex gap-2">
                        @if (config('company.social.facebook'))
                            <a href="{{ config('company.social.facebook') }}" class="footer-social-link" target="_blank" rel="noopener" aria-label="Facebook">
                                <i class="bi bi-facebook" aria-hidden="true"></i>
                            </a>
                        @endif
                        @if (config('company.social.instagram'))
                            <a href="{{ config('company.social.instagram') }}" class="footer-social-link" target="_blank" rel="noopener" aria-label="Instagram">
                                <i class="bi bi-instagram" aria-hidden="true"></i>
                            </a>
                        @endif
                        @if (config('company.social.tiktok'))
                            <a href="{{ config('company.social.tiktok') }}" class="footer-social-link" target="_blank" rel="noopener" aria-label="TikTok">
                                <i class="bi bi-tiktok" aria-hidden="true"></i>
                            </a>
                        @endif
                        <a href="https://wa.me/{{ config('company.whatsapp_number') }}" class="footer-social-link" target="_blank" rel="noopener" aria-label="{{ __('site.whatsapp.aria_label') }}">
                            <i class="bi bi-whatsapp" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="col-6 col-lg-4">
                    <span class="section-eyebrow section-eyebrow--light">{{ __('site.footer.links_label') }}</span>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">{{ __('site.nav.home') }}</a></li>
                        <li><a href="{{ route('about') }}">{{ __('site.nav.about') }}</a></li>
                        <li><a href="{{ route('services') }}">{{ __('site.nav.services') }}</a></li>
                        <li><a href="{{ route('work.index') }}">{{ __('site.nav.our_work') }}</a></li>
                        <li><a href="{{ route('contact') }}">{{ __('site.nav.contact') }}</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-4">
                    <span class="section-eyebrow section-eyebrow--light">{{ __('site.footer.contact_label') }}</span>
                    <ul class="footer-links">
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-telephone-fill" aria-hidden="true"></i>
                            <a href="tel:{{ config('company.phone') }}" dir="ltr">{{ config('company.phone') }}</a>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-envelope-fill" aria-hidden="true"></i>
                            <a href="mailto:{{ config('company.email') }}">{{ config('company.email') }}</a>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                            <a href="{{ config('company.map_url') }}" target="_blank" rel="noopener">{{ config('company.address') }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="footer-divider">

            <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-2">
                <p class="mb-0" style="color: rgba(255, 255, 255, .6);">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('site.footer.rights') }}</p>
                <span class="d-none d-sm-inline" style="color: rgba(255, 255, 255, .3);">&bull;</span>
                <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="footer-lang-link">
                    {{ __('site.nav.switch_language') }}
                </a>
            </div>
        </div>
    </footer>

    @include('partials.whatsapp-button')

    <x-loader />
    @include('partials.scripts')
    @stack('scripts')
</body>
</html>
