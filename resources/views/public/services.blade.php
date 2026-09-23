@extends('layouts.public')

@section('title', __('site.services.heading'))

@section('content')
<section class="py-4 reveal">
    <x-page-header
        :eyebrow="__('site.services.eyebrow')"
        :title="__('site.services.heading')"
        :lead="__('site.services.intro')"
    />

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
@endsection
