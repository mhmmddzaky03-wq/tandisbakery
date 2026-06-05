@php
    use App\Models\RawMaterial;
    use App\Support\FormatHelper;

    $indexRoute = $indexRoute ?? request()->route()->getName();

    $jumlahStok = (float) $material->jumlah;
    $minStok = (float) $material->min;
    $habis = $jumlahStok <= 0;
    $aman = ! $habis && $jumlahStok > $minStok;
    $statusLabel = $material->stockStatusLabel();
    $statusBadgeClass = $habis
        ? 'bg-rose-50 text-rose-600'
        : ($aman ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-700');
    $kategoriBadgeClass = match ($material->kategori) {
        RawMaterial::KATEGORI_KERING => 'bg-amber-50 text-amber-700',
        RawMaterial::KATEGORI_BASAH => 'bg-sky-50 text-sky-600',
        default => 'bg-violet-50 text-violet-700',
    };
    $productionCount = $usages->pluck('production_record_id')->unique()->count();
    $stockTone = $habis ? 'rose' : ($aman ? 'green' : 'amber');
    $stockValue = FormatHelper::formatQtyOne($material->jumlah).' '.$satuan;
    $stockSubDetail = __('stock.kpi_avg_price_sub', ['price' => FormatHelper::rupiah($material->harga), 'unit' => $satuan]);
    $usageSub = __('stock.kpi_usage_breakdown', ['production' => $productionCount, 'rows' => $material->material_usages_count]);
    $usageValue = FormatHelper::formatQtyOne($totalUsageQty).' '.$satuan;
    $hasStockBreakdown = $activeBatches->isNotEmpty() || $untrackedStock > 0 || $jumlahStok > 0;
@endphp

<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi-card
            :title="__('stock.kpi_current_stock')"
            :value="$stockValue"
            :sub="$statusLabel"
            :tone="$stockTone"
            icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>'
        />
        <x-kpi-card
            :title="__('stock.kpi_total_value')"
            :value="FormatHelper::rupiah($totalNilai)"
            :sub="$stockSubDetail"
            tone="amber"
            icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'
        />
        <x-kpi-card
            :title="__('stock.kpi_total_usage')"
            :value="$usageValue"
            :sub="$usageSub"
            tone="blue"
            icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>'
        />
        <x-kpi-card
            :title="__('stock.kpi_usage_value')"
            :value="FormatHelper::rupiah($totalUsageValue)"
            :sub="__('stock.kpi_usage_sub')"
            tone="violet"
            icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>'
        />
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header border-b border-slate-100 pb-4">
            <h2 class="text-base font-extrabold text-slate-900">{{ __('stock.section_info') }}</h2>
        </div>
        <div class="bakery-card-body">
            <dl class="grid gap-x-8 gap-y-0 sm:grid-cols-2">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('app.common.id') }}</dt>
                    <dd class="font-bold text-slate-800">{{ $material->id }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('app.common.category') }}</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $kategoriBadgeClass }}">
                            {{ $material->kategoriLabel() }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('app.common.unit') }}</dt>
                    <dd class="font-semibold uppercase text-slate-800">{{ $satuan }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('stock.field_min') }}</dt>
                    <dd><x-unit-qty :qty="$material->min" :unit="$satuan" qty-class="font-semibold text-slate-800" /></dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm sm:border-b-0">
                    <dt class="text-slate-400">{{ __('stock.label_avg_price') }}</dt>
                    <dd class="font-semibold text-slate-800">{{ FormatHelper::rupiah($material->harga) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('app.common.status') }}</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $statusBadgeClass }}">
                            {{ $statusLabel }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header bakery-card-header--bordered">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">{{ __('stock.section_batches') }}</h2>
            </div>
            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
                {{ __('app.common.total') }} {{ FormatHelper::formatQtyOne($material->jumlah) }} {{ $satuan }}
            </span>
        </div>
        <div class="bakery-card-body overflow-x-auto pt-2">
            @if ($hasStockBreakdown)
                <table class="bakery-table w-full min-w-[40rem]">
                    <colgroup>
                        <col class="w-[24%]" />
                        <col class="w-[18%]" />
                        <col class="w-[16%]" />
                        <col class="w-[20%]" />
                        <col class="w-[22%]" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="!pr-8">{{ __('stock.field_production_code') }}</th>
                            <th class="!pl-2">{{ __('stock.label_expired') }}</th>
                            <th>{{ __('stock.label_remaining') }}</th>
                            <th class="whitespace-nowrap">{{ __('app.common.price') }}</th>
                            <th class="whitespace-nowrap text-right">{{ __('stock.label_value') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activeBatches as $restock)
                            @php
                                $batchValue = (int) round((float) $restock->sisa * (int) $restock->harga);
                                $expiredClass = $restock->isExpired()
                                    ? 'text-rose-600 font-bold'
                                    : ($restock->isExpiringSoon() ? 'text-amber-700 font-bold' : 'text-slate-700');
                            @endphp
                            <tr>
                                <td class="!pr-8 align-top">
                                    <div class="font-semibold text-slate-800">{{ $restock->kode_produksi ?: '—' }}</div>
                                    <div class="mt-0.5 text-[11px] leading-snug text-slate-400">{{ __('stock.label_in') }} {{ FormatHelper::dateId($restock->tanggal) }}</div>
                                </td>
                                <td class="!pl-2 align-top whitespace-normal {{ $expiredClass }}">
                                    @if ($restock->expired)
                                        {{ FormatHelper::dateId($restock->expired) }}
                                        @if ($restock->isExpired())
                                            <span class="ml-1 rounded bg-rose-50 px-1 py-0.5 text-[10px] font-bold uppercase text-rose-600">{{ __('stock.badge_expired') }}</span>
                                        @elseif ($restock->isExpiringSoon())
                                            <span class="ml-1 rounded bg-amber-50 px-1 py-0.5 text-[10px] font-bold uppercase text-amber-700">{{ __('stock.badge_soon') }}</span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="align-top whitespace-nowrap font-bold text-emerald-700">
                                    {{ FormatHelper::formatQtyOne($restock->sisa) }} {{ $satuan }}
                                </td>
                                <td class="align-top whitespace-nowrap tabular-nums text-slate-700">{{ FormatHelper::rupiah($restock->harga) }}</td>
                                <td class="align-top whitespace-nowrap text-right tabular-nums font-bold text-slate-800">{{ FormatHelper::rupiah($batchValue) }}</td>
                            </tr>
                        @endforeach
                        @if ($untrackedStock > 0)
                            @php
                                $untrackedValue = (int) round($untrackedStock * (int) $material->harga);
                            @endphp
                            <tr>
                                <td class="!pr-8 align-top">
                                    <div class="font-semibold text-slate-800">{{ __('stock.badge_general_stock') }}</div>
                                    <div class="mt-0.5 text-[11px] leading-snug text-slate-400">{{ __('stock.badge_unbound_batch') }}</div>
                                </td>
                                <td class="!pl-2 align-top text-slate-400">—</td>
                                <td class="align-top whitespace-nowrap font-bold text-emerald-700">
                                    {{ FormatHelper::formatQtyOne($untrackedStock) }} {{ $satuan }}
                                </td>
                                <td class="align-top whitespace-nowrap tabular-nums text-slate-700">{{ FormatHelper::rupiah($material->harga) }}</td>
                                <td class="align-top whitespace-nowrap text-right tabular-nums font-bold text-slate-800">{{ FormatHelper::rupiah($untrackedValue) }}</td>
                            </tr>
                        @endif
                    </tbody>
                    @if ($activeBatches->isNotEmpty() || $untrackedStock > 0)
                        <tfoot>
                            <tr class="bg-emerald-50/60">
                                <td colspan="2" class="px-4 py-3 text-sm font-bold text-emerald-900">{{ __('stock.footer_current_total') }}</td>
                                <td class="px-4 py-3 text-sm font-extrabold text-emerald-800">
                                    {{ FormatHelper::formatQtyOne($material->jumlah) }} {{ $satuan }}
                                </td>
                                <td></td>
                                <td class="px-4 py-3 text-right text-sm font-extrabold whitespace-nowrap tabular-nums text-emerald-800">{{ FormatHelper::rupiah($totalNilai) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            @else
                <div class="py-10 text-center">
                    <p class="text-sm font-semibold text-slate-500">{{ __('stock.footer_out_of_stock') }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header bakery-card-header--bordered">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">{{ __('stock.section_dough_usage') }}</h2>
                <p class="mt-0.5 text-xs text-slate-500">{{ __('stock.section_dough_usage_sub') }}</p>
            </div>
            <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-bold text-violet-700">{{ ($adonanUsages ?? collect())->count() }} entri</span>
        </div>
        <div class="bakery-card-body overflow-x-auto pt-2">
            @if (($adonanUsages ?? collect())->isNotEmpty())
                <table class="bakery-table w-full min-w-[48rem]">
                    <thead>
                        <tr>
                            <th class="w-[6.5rem]">{{ __('app.common.date') }}</th>
                            <th>{{ __('bahan_dasar.back') }}</th>
                            <th class="w-[8rem]">{{ __('bahan_dasar.label_dough_batch') }}</th>
                            <th class="min-w-[8.5rem]">{{ __('stock.label_dose') }}</th>
                            <th class="w-[7rem]">{{ __('stock.label_price_unit') }}</th>
                            <th class="w-[6.5rem] text-right">{{ __('app.common.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($adonanUsages as $usage)
                            @php
                                $batch = $usage->batchBahanDasar;
                                $bahanDasar = $batch?->bahanDasar;
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap align-top text-slate-700">{{ $batch ? FormatHelper::dateId($batch->tanggal) : '—' }}</td>
                                <td class="align-top font-semibold text-slate-800">{{ $bahanDasar?->nama ?? '—' }}</td>
                                <td class="align-top text-sm font-semibold text-violet-700">#{{ $batch?->id ?? '—' }}</td>
                                <td class="align-top">
                                    <x-unit-qty :qty="$usage->jumlah" :unit="$usage->satuan" qty-class="font-semibold text-slate-800" anchor-toggle />
                                </td>
                                <td class="align-top text-slate-700">{{ FormatHelper::rupiah($usage->harga_satuan) }}</td>
                                <td class="align-top text-right font-bold text-slate-800">{{ FormatHelper::rupiah($usage->total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-violet-50/60">
                            <td colspan="5" class="px-4 py-3 text-right text-sm font-bold text-violet-900">{{ __('stock.footer_dough_usage') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-extrabold text-violet-800">{{ FormatHelper::rupiah($totalAdonanUsageValue ?? 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="py-10 text-center">
                    <p class="text-sm font-semibold text-slate-500">{{ __('stock.empty_dough_usage') }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header bakery-card-header--bordered">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">{{ __('stock.section_production_usage') }}</h2>
            </div>
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ $usages->count() }} entri</span>
        </div>
        <div class="bakery-card-body overflow-x-auto pt-2">
            @if ($usages->isNotEmpty())
                <table class="bakery-table w-full min-w-[52rem]">
                    <thead>
                        <tr>
                            <th class="w-[6.5rem]">{{ __('app.common.date') }}</th>
                            <th class="w-[5.5rem]">{{ __('stock.label_prod_id') }}</th>
                            <th class="w-[10rem]">{{ __('stock.label_product') }}</th>
                            <th class="min-w-[14rem]">{{ __('stock.label_stock_batch') }}</th>
                            <th class="min-w-[8.5rem]">{{ __('stock.label_dose') }}</th>
                            <th class="w-[7rem]">{{ __('stock.label_price_unit') }}</th>
                            <th class="w-[6.5rem] text-right">{{ __('app.common.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usages as $usage)
                            @php
                                $record = $usage->productionRecord;
                                $batch = $usage->restockBatch;
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap align-top text-slate-700">{{ $record ? FormatHelper::dateId($record->tanggal) : '—' }}</td>
                                <td class="align-top font-bold text-slate-800">{{ $record?->id ?? '—' }}</td>
                                <td class="align-top">
                                    <div class="max-w-[10rem] font-semibold leading-snug text-slate-800">{{ $record?->product_name ?? '—' }}</div>
                                    @if ($record?->status)
                                        <span class="text-[11px] {{ $record->status === 'Berhasil' ? 'text-emerald-600' : 'text-rose-600' }}">{{ \App\Support\LocaleLabels::productionStatus($record->status) }}</span>
                                    @endif
                                </td>
                                <td class="min-w-[14rem] align-top">
                                    @if ($batch)
                                        <div class="whitespace-normal text-sm leading-snug text-slate-700">
                                            @if ($batch->kode_produksi)
                                                <div class="font-semibold text-slate-800">{{ $batch->kode_produksi }}</div>
                                            @endif
                                            <div class="{{ $batch->kode_produksi ? 'mt-0.5 text-[11px] text-slate-500' : 'font-semibold text-slate-800' }}">
                                                {{ __('stock.badge_restock') }} {{ FormatHelper::dateId($batch->tanggal) }}
                                                @if ($batch->expired)
                                                    · {{ __('stock.label_expired') }} {{ FormatHelper::dateId($batch->expired) }}
                                                @endif
                                            </div>
                                            <div class="mt-0.5 text-[11px] font-semibold text-emerald-700">
                                                {{ __('stock.label_remaining') }} {{ FormatHelper::formatQtyOne($batch->sisa) }} {{ $satuan }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm font-semibold text-slate-600">{{ __('stock.badge_general_stock') }}</span>
                                    @endif
                                </td>
                                <td class="align-top">
                                    <x-unit-qty :qty="$usage->jumlah" :unit="$usage->satuan" qty-class="font-semibold text-slate-800" anchor-toggle />
                                </td>
                                <td class="align-top text-slate-700">{{ FormatHelper::rupiah($usage->harga_satuan) }}</td>
                                <td class="align-top text-right font-bold text-slate-800">{{ FormatHelper::rupiah($usage->total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-amber-50/60">
                            <td colspan="6" class="px-4 py-3 text-right text-sm font-bold text-amber-900">{{ __('stock.footer_production_usage') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-extrabold text-amber-800">{{ FormatHelper::rupiah($totalUsageValue) }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="py-10 text-center">
                    <p class="text-sm font-semibold text-slate-500">{{ __('stock.empty_production_usage') }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header bakery-card-header--bordered">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">{{ __('stock.section_restock_history') }}</h2>
            </div>
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ $restockHistory->count() }} batch</span>
        </div>
        <div class="bakery-card-body bakery-table-wrap pt-2">
            @if ($restockHistory->isNotEmpty())
                <table class="bakery-table">
                    <thead>
                        <tr>
                            <th class="w-[100px]">{{ __('app.common.date') }}</th>
                            <th>{{ __('stock.field_production_code') }}</th>
                            <th class="w-[110px]">{{ __('stock.label_expired') }}</th>
                            <th class="w-[90px]">{{ __('stock.label_in') }}</th>
                            <th class="w-[100px]">{{ __('app.common.price') }}</th>
                            <th class="w-[110px]">{{ __('app.common.total') }}</th>
                            <th>{{ __('stock.field_note') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($restockHistory as $restock)
                            @php
                                $expiredClass = $restock->isExpired()
                                    ? 'text-rose-600 font-bold'
                                    : ($restock->isExpiringSoon() ? 'text-amber-700 font-bold' : 'text-slate-700');
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap text-slate-700">{{ FormatHelper::dateId($restock->tanggal) }}</td>
                                <td class="font-semibold text-slate-800">{{ $restock->kode_produksi ?: '—' }}</td>
                                <td class="whitespace-nowrap {{ $expiredClass }}">
                                    @if ($restock->expired)
                                        {{ FormatHelper::dateId($restock->expired) }}
                                        @if ($restock->isExpired())
                                            <span class="ml-1 rounded bg-rose-50 px-1 py-0.5 text-[10px] font-bold uppercase text-rose-600">{{ __('stock.badge_expired') }}</span>
                                        @elseif ($restock->isExpiringSoon())
                                            <span class="ml-1 rounded bg-amber-50 px-1 py-0.5 text-[10px] font-bold uppercase text-amber-700">{{ __('stock.badge_soon') }}</span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="font-semibold text-slate-800">{{ FormatHelper::formatQtyOne($restock->jumlah) }} {{ $satuan }}</td>
                                <td class="text-slate-700">{{ FormatHelper::rupiah($restock->harga) }}</td>
                                <td class="text-right font-bold text-slate-800">{{ FormatHelper::rupiah($restock->total) }}</td>
                                <td class="max-w-[10rem] truncate text-slate-500" title="{{ $restock->catatan }}">{{ $restock->catatan ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="py-10 text-center">
                    <p class="text-sm font-semibold text-slate-500">{{ __('stock.empty_restock') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

@include('partials.stock-material-action-modals', ['m' => $material])
