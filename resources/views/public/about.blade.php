@extends('layouts.public')

@section('title', __('site.about.heading'))

@section('content')
<section class="py-4 reveal">
    <x-page-header
        :eyebrow="__('site.about.eyebrow')"
        :title="__('site.about.heading')"
        :lead="__('site.about.intro')"
    />
    <p class="fs-5" style="max-width: 720px;">{{ __('site.about.body') }}</p>
</section>
@endsection
