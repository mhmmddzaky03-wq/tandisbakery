@props([
    'route',
    'query' => [],
])

@php
    $params = array_filter($query, fn ($v) => $v !== null && $v !== '');
    $url = $route.(count($params) ? '?'.http_build_query($params) : '');
@endphp

<a
    href="{{ $url }}"
    target="_blank"
    rel="noopener noreferrer"
    {{ $attributes->merge(['class' => 'bakery-btn-ghost inline-flex items-center gap-2 whitespace-nowrap']) }}
    title="{{ __('app.common.print_pdf') }}"
>
    <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6M8 13h2m4 0h2M8 17h8" />
    </svg>
    <span>{{ __('app.common.print') }}</span>
</a>
