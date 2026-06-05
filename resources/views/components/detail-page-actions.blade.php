@props([
    'href' => '#',
    'label' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-end gap-2 sm:gap-2.5']) }}>
    <a href="{{ $href }}" class="detail-back-link">
        <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m15 18-6-6 6-6" />
        </svg>
        <span>{{ $label ?? __('app.common.back') }}</span>
    </a>

    @if (isset($toolbar) && ! $toolbar->isEmpty())
        <div class="bakery-action-toolbar" role="toolbar" aria-label="{{ __('app.common.page_actions') }}">
            {{ $toolbar }}
        </div>
    @endif
</div>
