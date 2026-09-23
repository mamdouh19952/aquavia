@extends('layouts.public')

@section('title', __('site.nav.home'))

@section('content')
<!-- Hero -->
<section class="hero-section mb-0" style="background-image: url('{{ asset('images/hero-pool.jpg') }}');">
    <div class="hero-inner">
        <span class="section-eyebrow section-eyebrow--light">{{ __('site.tagline') }}</span>
        <h1 class="display-4 mb-3">{{ __('site.home.hero_title') }}</h1>
        <div class="waterline mx-auto"></div>
        <p class="lead mb-4">{{ __('site.home.hero_subtitle') }}</p>
        <div class="hero-actions gap-2 d-flex justify-content-center flex-wrap">
            <a href="{{ route('work.index') }}" class="btn btn-primary btn-lg px-4">{{ __('site.home.hero_cta_work') }}</a>
            <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg px-4">{{ __('site.home.hero_cta_contact') }}</a>
        </div>
    </div>

    <div class="hero-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 100" preserveAspectRatio="none">
            <path class="hero-wave-fill" d="M0,40 C280,100 480,0 720,20 C960,40 1160,90 1440,30 L1440,100 L0,100 Z" />
        </svg>
    </div>
</section>

<!-- About teaser -->
<section class="py-5 reveal">
    <div class="row align-items-center g-5">
        <div class="col-md-6">
            <div class="image-panel shadow" style="background-image: url('{{ asset('images/about-pool.jpg') }}');"></div>
        </div>
        <div class="col-md-6">
            <span class="section-eyebrow">{{ __('site.home.about_eyebrow') }}</span>
            <h2 class="mb-0">{{ __('site.home.about_heading') }}</h2>
            <div class="waterline"></div>
            <p class="lead">{{ __('site.home.about_body') }}</p>
        </div>
    </div>
</section>

<!-- Services teaser -->
<section class="py-5 reveal">
    <div class="text-center mb-5">
        <span class="section-eyebrow justify-content-center">{{ __('site.home.services_eyebrow') }}</span>
        <h2 class="mb-0">{{ __('site.home.services_heading') }}</h2>
        <div class="waterline mx-auto"></div>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center py-5">
                    <span class="service-icon"><i class="bi bi-compass" aria-hidden="true"></i></span>
                    <h5 class="card-title">{{ __('site.services.design_title') }}</h5>
                    <p class="card-text text-muted">{{ __('site.services.design_body') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center py-5">
                    <span class="service-icon"><i class="bi bi-hammer" aria-hidden="true"></i></span>
                    <h5 class="card-title">{{ __('site.services.construction_title') }}</h5>
                    <p class="card-text text-muted">{{ __('site.services.construction_body') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center py-5">
                    <span class="service-icon"><i class="bi bi-droplet" aria-hidden="true"></i></span>
                    <h5 class="card-title">{{ __('site.services.maintenance_title') }}</h5>
                    <p class="card-text text-muted">{{ __('site.services.maintenance_body') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

@if ($featuredProjects->isNotEmpty())
<!-- Featured Work -->
<section class="py-5 reveal">
    <div class="text-center mb-5">
        <span class="section-eyebrow justify-content-center">{{ __('site.home.work_eyebrow') }}</span>
        <h2 class="mb-0">{{ __('site.home.work_heading') }}</h2>
        <div class="waterline mx-auto"></div>
    </div>
    <div class="row g-4">
        @foreach ($featuredProjects as $project)
            <div class="col-md-4">
                <a href="{{ route('work.show', $project) }}" class="text-decoration-none text-body">
                    <div class="card work-card h-100 shadow-sm">
                        @if ($project->thumbnail())
                            <div class="work-card-img-wrap">
                                <img
                                    src="{{ $project->thumbnail()->getUrl() }}"
                                    class="card-img-top"
                                    alt="{{ $project->title() }}"
                                    style="height: 240px; width: 100%; object-fit: cover;"
                                >
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title mb-2">{{ $project->title() }}</h5>
                            <span class="meta-tag"><i class="bi bi-geo-alt" aria-hidden="true"></i> {{ $project->location }}</span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    <div class="text-center mt-5">
        <a href="{{ route('work.index') }}" class="btn btn-outline-primary btn-lg px-4">{{ __('site.home.work_cta') }}</a>
    </div>
</section>
@endif

<!-- CTA -->
<section class="py-5 px-4 rounded-3 text-center reveal" style="background: linear-gradient(135deg, var(--brand-ink), var(--brand-cobalt)); color: #fff;">
    <span class="section-eyebrow section-eyebrow--light justify-content-center">{{ __('site.home.cta_eyebrow') }}</span>
    <h2 class="mb-0" style="color: #fff;">{{ __('site.home.cta_heading') }}</h2>
    <div class="waterline mx-auto"></div>
    <p class="lead mb-4" style="color: rgba(255,255,255,.85);">{{ __('site.home.cta_body') }}</p>
    <a href="{{ route('contact') }}" class="btn btn-lg px-4" style="background-color: var(--brand-aqua); border-color: var(--brand-aqua); color: var(--brand-ink); font-weight: 600;">{{ __('site.home.hero_cta_contact') }}</a>
</section>
@endsection
