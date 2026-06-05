@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
])

<a
    href="{{ $href }}"
    @class([
        'bakery-nav-link',
        'bakery-nav-link--active' => $active,
        'bakery-nav-link--idle' => ! $active,
    ])
>
    <span class="bakery-nav-link__icon">
        {!! $icon !!}
    </span>
    <span class="truncate">{{ $slot }}</span>
</a>
