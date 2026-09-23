@props([
    'eyebrow' => null,
    'title',
    'lead' => null,
    'center' => false,
])

<div {{ $attributes->class(['page-header mb-5', 'text-center' => $center]) }}>
    @if ($eyebrow)
        <span class="section-eyebrow">{{ $eyebrow }}</span>
    @endif

    <h1 class="mb-0">{{ $title }}</h1>
    <div @class(['waterline', 'mx-auto' => $center])></div>

    @if ($lead)
        <p class="lead mb-0">{{ $lead }}</p>
    @endif
</div>
