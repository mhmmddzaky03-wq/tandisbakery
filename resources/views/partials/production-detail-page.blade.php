@php
    use App\Support\FormatHelper;
    use App\Support\LocaleLabels;

    $isBerhasil = $record->status === 'Berhasil';
    $statusTone = $isBerhasil ? 'green' : 'rose';
    $statusBadgeClass = $isBerhasil
        ? 'bg-emerald-50 text-emerald-600'
        : 'bg-rose-50 text-rose-600';
    $hasilValue = number_format($record->jumlah, 0, ',', '.').' '.$record->satuan;
    $hasilSub = $isBerhasil ? __('production.kpi_result_success') : __('production.kpi_result_failed');

    if ($isBerhasil) {
        $stockContribValue = '+'.number_format($stockContribution, 0, ',', '.').' '.$record->satuan;
        $stockContribSub = $catalogProduct
            ? __('production.kpi_contribution_current', ['qty' => number_format($catalogProduct->jumlah, 0, ',', '.').' '.$catalogProduct->satuan])
            : __('production.kpi_contribution_none');
        $stockContribTone = $catalogProduct ? 'blue' : 'amber';
    } else {
        $stockContribValue = '—';
        $stockContribSub = __('production.kpi_contribution_failed');
        $stockContribTone = 'rose';
    }

    $materialSub = __('production.kpi_total_cost_sub', ['raw' => $materialCount, 'base' => $bahanDasarCount]);
    $totalMaterialCost = $record->total_material_cost;
    $totalBahanDasarCost = (int) $record->bahanDasarUsages->sum('total');
    $totalBahanBakuCost = (int) $record->materialUsages->sum('total');
@endphp

<div class="space-y-6">
    @if (! $isBerhasil)
        <div class="flex items-start gap-3 rounded-xl border border-rose-100 bg-rose-50/80 px-4 py-3 text-sm text-rose-800">
            <svg viewBox="0 0 24 24" class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>
            <div>
                <p class="font-bold">{{ __('production.alert_failed_title') }}</p>
                <p class="mt-0.5 text-rose-700">{{ __('production.alert_failed_body') }}</p>
            </div>
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi-card
            :title="__('production.kpi_status')"
            :value="LocaleLabels::productionStatus($record->status)"
            :sub="$isBerhasil ? __('production.kpi_status_success') : __('production.kpi_status_failed')"
            :tone="$statusTone"
            icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>'
        />
        <x-kpi-card
            :title="__('production.kpi_result')"
            :value="$hasilValue"
            :sub="$hasilSub"
            tone="amber"
            icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>'
        />
        <x-kpi-card
            :title="__('production.kpi_contribution')"
            :value="$stockContribValue"
            :sub="$stockContribSub"
            :tone="$stockContribTone"
            icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/></svg>'
        />
        <x-kpi-card
            :title="__('production.kpi_total_cost')"
            :value="FormatHelper::rupiah($totalMaterialCost)"
            :sub="$materialSub"
            tone="violet"
            icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'
        />
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header border-b border-slate-100 pb-4">
            <h2 class="text-base font-extrabold text-slate-900">{{ __('production.section_detail_info') }}</h2>
        </div>
        <div class="bakery-card-body">
            <dl class="grid gap-x-8 gap-y-0 sm:grid-cols-2">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('production.label_prod_id') }}</dt>
                    <dd class="font-bold text-slate-800">{{ $record->id }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('app.common.date') }}</dt>
                    <dd class="font-semibold text-slate-800">{{ FormatHelper::dateId($record->tanggal) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('production.field_product_name') }}</dt>
                    <dd class="text-right font-semibold text-slate-800">{{ $record->product_name }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('app.common.unit') }}</dt>
                    <dd class="font-semibold uppercase text-slate-800">{{ $record->satuan }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('app.common.status') }}</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $statusBadgeClass }}">
                            {{ LocaleLabels::productionStatus($record->status) }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 py-3 text-sm sm:border-b-0">
                    <dt class="shrink-0 text-slate-400">{{ __('production.label_catalog_product') }}</dt>
                    <dd class="text-right font-semibold text-slate-800">
                        @if ($catalogProduct)
                            <div>{{ $catalogProduct->id }}</div>
                            <div class="mt-0.5 text-xs font-normal text-slate-500">
                                {{ __('production.label_catalog_stock', ['qty' => number_format($catalogProduct->jumlah, 0, ',', '.').' '.$catalogProduct->satuan]) }}
                            </div>
                        @else
                            <span class="text-slate-400">{{ __('production.label_not_registered') }}</span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-start justify-between gap-4 py-3 text-sm sm:col-span-2">
                    <dt class="shrink-0 text-slate-400">{{ __('production.field_note') }}</dt>
                    <dd class="max-w-prose text-right text-slate-700">{{ $record->notes ?: '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    @if ($record->bahanDasarUsages->isNotEmpty())
        <div class="bakery-card">
            <div class="bakery-card-header bakery-card-header--bordered">
                <h2 class="text-base font-extrabold text-slate-900">{{ __('production.section_base_usage') }}</h2>
                <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-bold text-violet-700">{{ __('production.row_count', ['count' => $bahanDasarCount]) }}</span>
            </div>
            <div class="bakery-card-body overflow-x-auto pt-2">
                <table class="bakery-table w-full min-w-[48rem]">
                    <thead>
                        <tr>
                            <th class="w-[10rem]">{{ __('production.label_base_material') }}</th>
                            <th class="min-w-[14rem]">{{ __('production.section_base_batch') }}</th>
                            <th class="min-w-[8.5rem]">{{ __('production.label_dose') }}</th>
                            <th class="w-[7rem]">{{ __('production.label_price_unit') }}</th>
                            <th class="w-[6.5rem] text-right">{{ __('app.common.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($record->bahanDasarUsages as $usage)
                            @php $batch = $usage->batchBahanDasar; @endphp
                            <tr>
                                <td class="align-top">
                                    <div class="font-semibold leading-snug text-slate-800">{{ $usage->bahanDasar?->nama ?? $usage->bahan_dasar_id }}</div>
                                    <div class="mt-0.5 text-[11px] text-slate-500">{{ $usage->bahan_dasar_id }}</div>
                                </td>
                                <td class="min-w-[14rem] align-top text-sm text-slate-700">
                                    @if ($batch)
                                        {{ FormatHelper::dateId($batch->tanggal) }} · batch #{{ $batch->id }}
                                    @else
                                        —
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
                        <tr class="bg-violet-50/60">
                            <td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-violet-900">{{ __('production.footer_base_total') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-extrabold text-violet-800">{{ FormatHelper::rupiah($totalBahanDasarCost) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    <div class="bakery-card">
        <div class="bakery-card-header bakery-card-header--bordered">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">{{ __('production.section_raw_usage') }}</h2>
            </div>
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ __('production.row_count', ['count' => $materialCount]) }}</span>
        </div>
        <div class="bakery-card-body overflow-x-auto pt-2">
            @if ($record->materialUsages->isNotEmpty())
                <table class="bakery-table w-full min-w-[48rem]">
                    <thead>
                        <tr>
                            <th class="w-[10rem]">{{ __('production.label_raw_material') }}</th>
                            <th class="min-w-[14rem]">{{ __('production.label_stock_batch') }}</th>
                            <th class="min-w-[8.5rem]">{{ __('production.label_dose') }}</th>
                            <th class="w-[7rem]">{{ __('production.label_price_unit') }}</th>
                            <th class="w-[6.5rem] text-right">{{ __('app.common.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($record->materialUsages as $usage)
                            @php $batch = $usage->restockBatch; @endphp
                            <tr>
                                <td class="align-top">
                                    <div class="font-semibold leading-snug text-slate-800">{{ $usage->rawMaterial?->nama ?? $usage->raw_material_id }}</div>
                                    <div class="mt-0.5 text-[11px] text-slate-500">{{ $usage->raw_material_id }}</div>
                                </td>
                                <td class="min-w-[14rem] align-top">
                                    @if ($batch)
                                        <div class="whitespace-normal text-sm leading-snug text-slate-700">
                                            @if ($batch->kode_produksi)
                                                <div class="font-semibold text-slate-800">{{ $batch->kode_produksi }}</div>
                                            @endif
                                            <div class="{{ $batch->kode_produksi ? 'mt-0.5 text-[11px] text-slate-500' : 'font-semibold text-slate-800' }}">
                                                {{ __('production.badge_restock') }} {{ FormatHelper::dateId($batch->tanggal) }}
                                                @if ($batch->expired)
                                                    · Exp {{ FormatHelper::dateId($batch->expired) }}
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-sm font-semibold text-slate-600">{{ __('production.badge_general_stock') }}</span>
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
                            <td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-amber-900">{{ __('production.footer_raw_total') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-extrabold text-amber-800">{{ FormatHelper::rupiah($totalBahanBakuCost) }}</td>
                        </tr>
                        @if ($record->bahanDasarUsages->isNotEmpty())
                            <tr class="bg-violet-50/40">
                                <td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-slate-700">{{ __('production.footer_grand_total') }}</td>
                                <td class="px-4 py-3 text-right text-sm font-extrabold text-slate-900">{{ FormatHelper::rupiah($totalMaterialCost) }}</td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            @else
                <div class="py-10 text-center">
                    <p class="text-sm font-semibold text-slate-500">{{ __('production.empty_raw_usage') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
