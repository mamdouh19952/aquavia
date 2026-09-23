@extends('layouts.public')

@section('title', __('site.work.not_found_title'))

@section('content')
<section class="py-5 text-center">
    <div class="mb-4" style="color: var(--brand-aqua); font-size: 3.5rem;">
        <i class="bi bi-life-preserver" aria-hidden="true"></i>
    </div>
    <x-page-header
        :title="__('site.work.not_found_title')"
        :lead="__('site.work.not_found_body')"
        center
    />
    <a href="{{ route('work.index') }}" class="btn btn-primary">{{ __('site.work.back_to_work') }}</a>
</section>
@endsection
