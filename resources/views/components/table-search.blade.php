@props([
    'placeholder' => '',
    'value' => '',
])

<div {{ $attributes->merge(['class' => 'bakery-search w-full min-w-0 sm:w-[240px] sm:shrink-0']) }}>
    <svg viewBox="0 0 24 24" class="h-5 w-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.2-4.2" />
    </svg>
    <input
        type="search"
        class="min-w-0 flex-1 bg-transparent text-sm text-slate-700 placeholder:text-slate-400 outline-none"
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        autocomplete="off"
        aria-label="{{ $placeholder ?: 'Cari' }}"
        data-table-search-input
    />
</div>
