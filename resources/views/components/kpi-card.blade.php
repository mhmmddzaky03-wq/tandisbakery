@props([
    'title' => '',
    'value' => '',
    'sub' => '',
    'trend' => null,
    'icon' => null,
    'tone' => 'amber',
])

@php
    $toneMap = [
        'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'value' => 'text-slate-900'],
        'green' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'value' => 'text-slate-900'],
        'blue' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-600', 'value' => 'text-slate-900'],
        'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'value' => 'text-slate-900'],
        'violet' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'value' => 'text-slate-900'],
    ];
    $t = $toneMap[$tone] ?? $toneMap['amber'];
    $isUp = $trend && str_starts_with((string) $trend, '+');
@endphp

<div {{ $attributes->merge(['class' => 'bakery-card min-w-0']) }}>
    <div class="p-4 sm:p-5">
        <div class="flex items-center gap-3">
            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl {{ $t['bg'] }} {{ $t['text'] }}">
                @if ($icon)
                    {!! $icon !!}
                @else
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <path d="M8 12h8M12 8v8" />
                    </svg>
                @endif
            </div>
            <p class="min-w-0 flex-1 text-xs font-semibold leading-snug text-slate-500">{{ $title }}</p>
        </div>

        <p class="mt-3 break-words text-lg font-extrabold leading-tight tabular-nums sm:text-xl {{ $t['value'] }}">
            {{ $value }}
        </p>

        @if ($trend || $sub)
            <div class="mt-2.5 flex flex-wrap items-center gap-x-2 gap-y-1">
                @if ($trend)
                    <span class="inline-flex shrink-0 rounded-full px-2 py-0.5 text-[11px] font-bold {{ $isUp ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                        {{ $trend }}
                    </span>
                @endif
                @if ($sub)
                    <span class="text-[11px] font-medium leading-snug text-slate-400">{{ $sub }}</span>
                @endif
            </div>
        @endif
    </div>
</div>
