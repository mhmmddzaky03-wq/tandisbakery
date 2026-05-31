@php use App\Support\FormatHelper; @endphp

<details
    class="group overflow-hidden rounded-2xl bg-white ring-1 ring-slate-100 transition hover:ring-slate-200"
    {{ ($openFirst ?? false) ? 'open' : '' }}
>
    <summary class="flex cursor-pointer list-none items-center gap-3 px-4 py-3.5 marker:content-none sm:px-5">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-open:rotate-90 group-open:bg-amber-100 group-open:text-amber-800">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6" />
            </svg>
        </span>
        <span class="min-w-0 flex-1">
            <span class="block text-sm font-bold text-slate-800">{{ $section['label'] }}</span>
            <span class="text-xs text-slate-500">{{ $section['lines']->count() }} akun</span>
        </span>
        <span class="shrink-0 text-right">
            <span class="block text-sm font-extrabold tabular-nums text-slate-900">{{ FormatHelper::rupiah($section['subtotal']) }}</span>
        </span>
    </summary>

    <div class="border-t border-slate-100 px-4 pb-3 sm:px-5">
        <ul class="divide-y divide-slate-50">
            @foreach ($section['lines'] as $line)
                <li class="flex items-start justify-between gap-3 py-2.5">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-slate-800">{{ $line['kode'] }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $line['nama'] }}</p>
                    </div>
                    <p class="shrink-0 text-sm font-semibold tabular-nums text-slate-700">{{ FormatHelper::rupiah($line['saldo']) }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</details>
