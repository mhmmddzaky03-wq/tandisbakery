@props([
    'placeholder' => '',
    'value' => '',
])

<div {{ $attributes->merge(['class' => 'flex w-[220px] shrink-0 items-center gap-3 rounded-2xl bg-white px-4 py-2.5 ring-1 ring-black/5 sm:w-[240px]']) }}>
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
        data-table-search-input
    />
</div>
