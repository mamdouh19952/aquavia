@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
])

<button
    type="{{ $type }}"
    {{ $attributes->class([
        'btn',
        'btn-' . $variant,
        match($size) {
            'sm' => 'btn-sm',
            'lg' => 'btn-lg',
            default => '',
        },
        'disabled' => $disabled,
    ]) }}
    @disabled($disabled)
>
    {{ $slot }}
</button>
