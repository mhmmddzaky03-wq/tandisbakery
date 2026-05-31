@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $title = 'Dashboard Admin';
    $role = 'admin';
    $active = 'admin.dashboard';
    $pageTitle = 'Dashboard';
    $pageSubtitle = 'Ringkasan operasional & keuangan · ' . ($monthName ?? now()->format('F Y'));
    $ic = fn ($d) => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'" /></svg>';
    $hasSalesChart = collect($salesChart['values'] ?? [])->sum() > 0;
    $hasCostChart = ($totalCostComposition ?? 0) > 0;
    $financeMax = max(1, abs($netProfit), $grossProfit, $salesMonth);
    $profitMargin = $salesMonth > 0 ? round(($netProfit / $salesMonth) * 100, 1) : 0;
    $financeRows = [
        [
            'label' => 'Penjualan',
            'hint' => 'Total transaksi bulan ini',
            'value' => $salesMonth,
            'bar' => 'bg-amber-500',
            'text' => 'text-amber-800',
            'icon_bg' => 'bg-amber-100 text-amber-600',
            'icon' => 'M4 7h16M4 12h16M4 17h10',
        ],
        [
            'label' => 'Laba kotor',
            'hint' => 'Pendapatan − HPP (jurnal)',
            'value' => $grossProfit,
            'bar' => 'bg-sky-500',
            'text' => 'text-sky-800',
            'icon_bg' => 'bg-sky-100 text-sky-600',
            'icon' => 'M3 3v18h18M7 16l4-4 4 4 5-8',
        ],
        [
            'label' => 'Laba bersih',
            'hint' => 'Setelah beban operasional & pajak',
            'value' => $netProfit,
            'bar' => $netProfit >= 0 ? 'bg-emerald-500' : 'bg-rose-500',
            'text' => $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-600',
            'icon_bg' => $netProfit >= 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600',
            'icon' => 'M12 8c-2.5 1.5-5 3-5 6a5 5 0 0 0 10 0c0-3-2.5-4.5-5-6Z',
        ],
    ];
@endphp

@section('content')
<div class="space-y-5">
    {{-- KPI --}}
    <div class="grid grid-cols-1 gap-4 min-[420px]:grid-cols-2 xl:grid-cols-4">
        <x-kpi-card
            title="Total Stok Bahan"
            :value="(string) $stockCount"
            :sub="$lowStockCount.' di bawah minimum'"
            tone="rose"
            :icon="$ic('M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4')"
        />
        <x-kpi-card
            title="Produksi Berhasil"
            :value="(string) $successProduction"
            :sub="$totalProduction > 0 ? $failedProduction.' gagal dari '.$totalProduction.' batch' : 'Belum ada produksi'"
            tone="green"
            :icon="$ic('M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z')"
        />
        <x-kpi-card
            title="Biaya Operasional"
            :value="FormatHelper::rupiah($operationalMonth)"
            sub="Bulan berjalan"
            tone="blue"
            :icon="$ic('M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2')"
        />
        <x-kpi-card
            title="Penjualan (30 hari)"
            :value="FormatHelper::rupiah($salesLast30)"
            sub="vs 7 hari sebelumnya"
            :trend="$salesTrendLabel"
            tone="amber"
            :icon="$ic('M13 7h8m0 0v8m0-8l-8 8-4-4-6 6')"
        />
    </div>

    {{-- Grafik utama --}}
    <div class="grid gap-5 lg:grid-cols-3">
        <div class="bakery-card overflow-hidden lg:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 bg-gradient-to-r from-amber-50/80 to-white px-4 py-4 sm:px-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-amber-800/70">Analitik</p>
                    <h2 class="text-base font-extrabold text-slate-900">Tren Penjualan</h2>
                    <p class="mt-0.5 text-xs text-slate-500">14 hari terakhir · total {{ FormatHelper::rupiah($salesChart['total']) }}</p>
                </div>
                @if ($hasSalesChart && $salesChart['peak'] > 0)
                    <div class="rounded-xl bg-white px-3 py-2 text-right ring-1 ring-amber-200/60">
                        <p class="text-[10px] font-bold uppercase text-amber-800/60">Puncak</p>
                        <p class="text-sm font-extrabold tabular-nums text-amber-700">{{ FormatHelper::rupiah($salesChart['peak']) }}</p>
                        <p class="text-[11px] text-slate-500">{{ $salesChart['peak_label'] }}</p>
                    </div>
                @endif
            </div>
            <div class="p-4 sm:p-5">
                @if ($hasSalesChart)
                    <div class="relative h-[260px] w-full sm:h-[280px]">
                        <canvas
                            id="sales-trend-chart"
                            data-chart='@json($salesChart)'
                            aria-label="Grafik tren penjualan 14 hari"
                        ></canvas>
                    </div>
                @else
                    <div class="flex h-[220px] flex-col items-center justify-center rounded-2xl bg-slate-50 text-center ring-1 ring-slate-100">
                        <div class="mb-3 grid h-12 w-12 place-items-center rounded-2xl bg-amber-100 text-amber-600">
                            {!! $ic('M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z') !!}
                        </div>
                        <p class="font-semibold text-slate-600">Belum ada penjualan tercatat</p>
                        <p class="mt-1 max-w-xs text-sm text-slate-500">Grafik akan muncul setelah transaksi penjualan masuk ke sistem.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bakery-card overflow-hidden">
            <div class="border-b border-slate-100 bg-gradient-to-br from-sky-50/80 to-white px-4 py-4 sm:px-5">
                <p class="text-xs font-bold uppercase tracking-wide text-sky-800/70">Bulan ini</p>
                <h2 class="text-base font-extrabold text-slate-900">Komposisi Biaya</h2>
                <p class="mt-0.5 text-xs text-slate-500">{{ $monthName }}</p>
            </div>
            <div class="p-4 sm:p-5">
                @if ($hasCostChart)
                    <div class="relative mx-auto h-[200px] w-full max-w-[240px]">
                        <canvas
                            id="cost-composition-chart"
                            data-chart='@json($costChart)'
                            aria-label="Grafik komposisi biaya operasional"
                        ></canvas>
                    </div>
                    <p class="mt-3 text-center text-xs font-semibold text-slate-500">
                        Total {{ FormatHelper::rupiah($totalCostComposition) }}
                    </p>
                @else
                    <div class="flex h-[200px] flex-col items-center justify-center rounded-2xl bg-slate-50 text-center text-sm text-slate-500 ring-1 ring-slate-100">
                        Belum ada biaya operasional bulan ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Keuangan & produksi --}}
    <div class="grid gap-4 lg:grid-cols-2 lg:items-stretch">
        <div class="bakery-card flex h-full min-h-0 flex-col overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-gradient-to-r from-emerald-50/60 to-white px-4 py-3 sm:px-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-emerald-800/60">Keuangan</p>
                    <h2 class="text-sm font-extrabold text-slate-900">{{ $monthName }}</h2>
                </div>
                <a href="{{ route('admin.laba_rugi') }}" class="text-xs font-bold text-amber-700 hover:text-amber-800">
                    Lihat laba rugi →
                </a>
            </div>

            <div class="flex flex-1 flex-col justify-between gap-3 p-4 sm:p-5">
                @foreach ($financeRows as $row)
                    @php $pct = min(100, (int) round((abs($row['value']) / $financeMax) * 100)); @endphp
                    <div class="rounded-xl bg-slate-50/90 p-3.5 ring-1 ring-slate-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-start gap-2.5">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl {{ $row['icon_bg'] }}">
                                    {!! $ic($row['icon']) !!}
                                </span>
                                <div class="min-w-0 pt-0.5">
                                    <p class="text-sm font-bold text-slate-800">{{ $row['label'] }}</p>
                                    <p class="text-[11px] leading-snug text-slate-500">{{ $row['hint'] }}</p>
                                </div>
                            </div>
                            <p class="shrink-0 text-right text-sm font-extrabold leading-tight tabular-nums sm:text-base {{ $row['text'] }}">
                                {{ FormatHelper::rupiah($row['value']) }}
                            </p>
                        </div>
                        <div class="mt-2.5 h-2 overflow-hidden rounded-full bg-white ring-1 ring-slate-100">
                            <div class="h-full rounded-full transition-all {{ $row['bar'] }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-auto flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 bg-slate-50/80 px-4 py-3 text-xs sm:px-5">
                <span class="text-slate-500">Margin laba bersih</span>
                <span class="font-extrabold tabular-nums {{ $profitMargin >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                    {{ $profitMargin }}% dari penjualan
                </span>
            </div>
        </div>

        <div class="bakery-card flex h-full min-h-0 flex-col overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-gradient-to-r from-sky-50/60 to-white px-4 py-3 sm:px-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-sky-800/60">Operasional</p>
                    <h2 class="text-sm font-extrabold text-slate-900">Ringkasan produksi</h2>
                </div>
                <a href="{{ route('admin.produksi') }}" class="text-xs font-bold text-amber-700 hover:text-amber-800">
                    Kelola produksi →
                </a>
            </div>

            @if ($totalProduction > 0)
                <div class="flex flex-1 flex-col p-4 sm:p-5">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-emerald-50/80 p-3 text-center ring-1 ring-emerald-100">
                                <p class="text-2xl font-extrabold tabular-nums text-emerald-700">{{ $successProduction }}</p>
                                <p class="mt-0.5 text-xs font-semibold text-emerald-800/70">Berhasil</p>
                            </div>
                            <div class="rounded-xl bg-rose-50/80 p-3 text-center ring-1 ring-rose-100">
                                <p class="text-2xl font-extrabold tabular-nums text-rose-700">{{ $failedProduction }}</p>
                                <p class="mt-0.5 text-xs font-semibold text-rose-800/70">Gagal</p>
                            </div>
                        </div>

                        <div>
                            <div class="mb-1.5 flex justify-between text-xs font-semibold text-slate-500">
                                <span>Tingkat keberhasilan</span>
                                <span class="tabular-nums text-slate-700">{{ $productionSuccessRate }}%</span>
                            </div>
                            <div class="relative h-3 overflow-hidden rounded-full bg-orange-600 ring-1 ring-inset ring-black/10">
                                <div
                                    class="absolute inset-y-0 left-0 rounded-full bg-green-700 transition-all"
                                    style="width: {{ $productionSuccessRate }}%"
                                ></div>
                            </div>
                        </div>

                        @if ($latestProduction)
                            <div class="rounded-xl bg-slate-50/90 px-3.5 py-3 ring-1 ring-slate-100">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Batch terakhir</p>
                                <p class="mt-1 truncate text-sm font-bold text-slate-800">{{ $latestProduction->product_name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ FormatHelper::dateId($latestProduction->tanggal) }}
                                    · {{ $latestProduction->jumlah }} {{ $latestProduction->satuan }}
                                    · <span class="{{ $latestProduction->status === 'Berhasil' ? 'font-semibold text-emerald-600' : 'font-semibold text-rose-600' }}">{{ $latestProduction->status }}</span>
                                </p>
                            </div>
                        @endif
                    </div>

                    @if ($recentFailedProduction && $recentFailedProduction->id !== $latestProduction?->id)
                        <div class="mt-auto flex items-start gap-2 rounded-xl bg-rose-50 px-3 py-2.5 pt-3 ring-1 ring-rose-100">
                            <span class="shrink-0 text-rose-600">{!! $ic('M12 9v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z') !!}</span>
                            <p class="min-w-0 text-xs leading-relaxed text-rose-800">
                                <span class="font-bold">{{ $recentFailedProduction->product_name }}</span>
                                gagal — cek catatan di menu produksi.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="mt-auto flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 bg-slate-50/80 px-4 py-3 text-xs sm:px-5">
                    <span class="text-slate-500">{{ $totalProduction }} batch tercatat</span>
                    <span class="font-extrabold tabular-nums text-sky-700">{{ $productionSuccessRate }}% sukses</span>
                </div>
            @else
                <div class="flex flex-1 flex-col items-center justify-center p-6 text-center">
                    <p class="text-sm font-semibold text-slate-600">Belum ada produksi</p>
                    <p class="mt-1 text-xs text-slate-500">Catat batch pertama di menu Data Produksi.</p>
                    <a href="{{ route('admin.produksi') }}" class="mt-3 inline-block text-xs font-bold text-amber-700 hover:text-amber-800">
                        Buka data produksi →
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Aktivitas --}}
    <div class="bakery-card overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/80 px-4 py-3 sm:px-5">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Sistem</p>
            <h2 class="text-sm font-extrabold text-slate-900">Log Aktivitas Terbaru</h2>
        </div>
        <ul class="divide-y divide-slate-50 px-4 py-2 sm:px-5">
            @forelse ($activityLogs as $log)
                <li class="flex gap-3 py-3.5">
                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-amber-400 ring-4 ring-amber-100"></span>
                    <p class="min-w-0 flex-1 text-sm leading-relaxed text-slate-700">{{ $log->formatted_log }}</p>
                </li>
            @empty
                <li class="py-12 text-center text-sm text-slate-500">Belum ada aktivitas tercatat.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
