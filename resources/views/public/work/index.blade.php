@extends('layouts.public')

@section('title', __('site.work.heading'))

@section('content')
<section class="py-4 reveal">
    <x-page-header
        :eyebrow="__('site.work.eyebrow')"
        :title="__('site.work.heading')"
        :lead="__('site.work.intro')"
    />

    @if ($projects->isEmpty())
        <div class="alert alert-info" role="alert">
            {{ __('site.work.empty_state') }}
        </div>
    @else
        <div class="row g-4">
            @foreach ($projects as $project)
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
    @endif
</section>
@endsection
