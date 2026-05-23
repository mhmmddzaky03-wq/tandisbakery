@props([
    'id',
    'title' => '',
    'subtitle' => null,
    'size' => 'md',
    'autoOpen' => false,
])

@php
    $widthClass = match ($size) {
        'sm' => 'max-w-md',
        'lg' => 'max-w-2xl',
        default => 'max-w-lg',
    };
@endphp

<dialog
    data-modal="{{ $id }}"
    @if ($autoOpen) data-auto-open="true" @endif
    class="bakery-modal {{ $widthClass }} w-[calc(100%-2rem)] rounded-2xl border-0 bg-transparent p-0 shadow-none backdrop:bg-black/40"
    aria-labelledby="modal-title-{{ $id }}"
>
    <div class="bakery-card flex max-h-[min(90vh,680px)] flex-col overflow-hidden !rounded-2xl shadow-xl ring-1 ring-black/10">
        <div class="flex shrink-0 items-start justify-between gap-3 border-b border-slate-100 px-5 py-4">
            <div class="min-w-0 flex-1">
                <h2 id="modal-title-{{ $id }}" class="text-base font-extrabold text-slate-900">{{ $title }}</h2>
                @if ($subtitle)
                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $subtitle }}</p>
                @endif
            </div>
            <button
                type="button"
                class="bakery-modal-close grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-slate-50 text-slate-600 ring-1 ring-black/5 transition hover:bg-slate-100"
                data-modal-close
                aria-label="{{ __('ui.close') }}"
            >
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" d="M6 6l12 12M18 6 6 18" />
                </svg>
            </button>
        </div>
        <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-5 py-4">
            {{ $slot }}
        </div>
    </div>
</dialog>
