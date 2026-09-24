@extends('layouts.public')

@section('title', __('site.contact.heading'))

@section('content')
<section class="py-4 reveal">
    <x-page-header
        :eyebrow="__('site.contact.eyebrow')"
        :title="__('site.contact.heading')"
        :lead="__('site.contact.intro')"
    />

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-4">
                    <ul class="list-unstyled fs-5 mb-4">
                        <li class="d-flex align-items-center gap-3 mb-3">
                            <i class="bi bi-telephone-fill" style="color: var(--brand-aqua-dark);" aria-hidden="true"></i>
                            <a href="tel:{{ config('company.phone') }}" class="text-decoration-none" dir="ltr">{{ config('company.phone') }}</a>
                        </li>
                        <li class="d-flex align-items-center gap-3 mb-3">
                            <i class="bi bi-envelope-fill" style="color: var(--brand-aqua-dark);" aria-hidden="true"></i>
                            <a href="mailto:{{ config('company.email') }}" class="text-decoration-none">{{ config('company.email') }}</a>
                        </li>
                        <li class="d-flex align-items-center gap-3">
                            <i class="bi bi-geo-alt-fill" style="color: var(--brand-aqua-dark);" aria-hidden="true"></i>
                            <a href="{{ config('company.map_url') }}" class="text-decoration-none" target="_blank" rel="noopener">
                                {{ config('company.address') }}
                            </a>
                        </li>
                    </ul>

                    <a href="https://wa.me/{{ config('company.whatsapp_number') }}" class="btn btn-success btn-lg" target="_blank" rel="noopener">
                        <i class="bi bi-whatsapp me-1" aria-hidden="true"></i> {{ __('site.contact.whatsapp_cta') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-4">
                    <span class="section-eyebrow">{{ __('site.contact.social_label') }}</span>
                    <div class="d-flex gap-4 fs-5 mt-2">
                        @if (config('company.social.facebook'))
                            <a href="{{ config('company.social.facebook') }}" class="text-decoration-none" target="_blank" rel="noopener">Facebook</a>
                        @endif
                        @if (config('company.social.instagram'))
                            <a href="{{ config('company.social.instagram') }}" class="text-decoration-none" target="_blank" rel="noopener">Instagram</a>
                        @endif
                        @if (config('company.social.tiktok'))
                            <a href="{{ config('company.social.tiktok') }}" class="text-decoration-none" target="_blank" rel="noopener">TikTok</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
