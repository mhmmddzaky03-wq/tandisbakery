@php use App\Support\FormatHelper; @endphp

<div class="space-y-3">
    @forelse ($sections as $section)
        @php
            $pct = $total > 0 ? min(100, (int) round(($section['subtotal'] / $total) * 100)) : 0;
        @endphp
        <div>
            <div class="mb-1.5 flex items-baseline justify-between gap-2 text-sm">
                <span class="font-semibold text-slate-700">{{ $section['label'] }}</span>
                <span class="shrink-0 font-extrabold tabular-nums text-slate-900">{{ FormatHelper::rupiah($section['subtotal']) }}</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                <div
                    class="h-full rounded-full transition-all {{ $barClass ?? 'bg-sky-500' }}"
                    style="width: {{ $pct }}%"
                    title="{{ $pct }}%"
                ></div>
            </div>
        </div>
    @empty
        <p class="rounded-xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-400">Tidak ada saldo</p>
    @endforelse
</div>
