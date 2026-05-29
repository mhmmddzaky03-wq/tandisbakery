@props([
    'title' => '',
    'value' => '',
    'sub' => '',
    'trend' => null, // "+12.5%" / "-5.2%"
    'icon' => null,
    'tone' => 'amber', // amber|green|blue|rose
])

@php
    $toneMap = [
        'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
        'green' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
        'blue' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-600'],
        'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600'],
        'violet' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600'],
    ];
    $t = $toneMap[$tone] ?? $toneMap['amber'];
@endphp

<div class="bakery-card">
    <div class="bakery-card-body pt-5">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="grid h-10 w-10 flex-shrink-0 place-items-center rounded-2xl {{ $t['bg'] }} {{ $t['text'] }}">
                    @if ($icon)
                        {!! $icon !!}
                    @else
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <path d="M8 12h8M12 8v8" />
                        </svg>
                    @endif
                </div>
                <div class="leading-tight min-w-0">
                    <div class="text-xs font-semibold text-slate-400 truncate">{{ $title }}</div>
                    <div class="text-2xl font-bold text-slate-800 truncate">{{ $value }}</div>
                </div>
            </div>

            @if ($trend)
                @php $isUp = str_starts_with((string) $trend, '+'); @endphp
                <div class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold flex-shrink-0 {{ $isUp ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                    <span>{{ $trend }}</span>
                </div>
            @endif
        </div>

        @if ($sub)
            <div class="mt-3 text-xs font-semibold text-slate-400">{{ $sub }}</div>
        @endif
    </div>
</div>

