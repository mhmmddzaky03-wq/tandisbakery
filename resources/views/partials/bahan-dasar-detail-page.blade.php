@php
    use App\Support\FormatHelper;
    use App\Support\LocaleLabels;

    $updateRoute = $updateRoute ?? 'admin.bahan_dasar.update';
    $buatAdonanRoute = $buatAdonanRoute ?? 'admin.bahan_dasar.buat_adonan';
    $destroyBatchRoute = $destroyBatchRoute ?? 'admin.bahan_dasar.batch.destroy';

    $jumlahStok = (float) $item->jumlah;
    $minStok = (float) $item->min;
    $habis = $jumlahStok <= 0;
    $aman = ! $habis && $jumlahStok > $minStok;
    $statusLabel = LocaleLabels::stockStatus($jumlahStok, $minStok);
    $statusBadgeClass = $habis
        ? 'bg-rose-50 text-rose-600'
        : ($aman ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-700');
    $stockValue = FormatHelper::formatQtyOne($item->jumlah).' '.$satuan;
    $stockSub = __('stock.kpi_batch_sub', ['count' => $activeBatches->count(), 'status' => $statusLabel]);
    $hargaValue = FormatHelper::rupiah($item->harga);
    $hargaSub = __('bahan_dasar.kpi_avg_price_sub', ['unit' => $satuan]);
    $batchValue = (string) $item->batches_count;
    $batchSub = __('bahan_dasar.kpi_batches_remaining', ['count' => $activeBatches->count()]);
    $nilaiValue = FormatHelper::rupiah($totalNilai);
    $nilaiSub = __('bahan_dasar.kpi_total_value_sub');

    $allPemakaian = $batches->flatMap(fn ($b) => $b->pemakaianBahanBaku)->values();
@endphp

<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi-card :title="__('bahan_dasar.kpi_current_stock')" :value="$stockValue" :sub="$stockSub" tone="green" icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>' />
        <x-kpi-card :title="__('bahan_dasar.kpi_avg_price')" :value="$hargaValue" :sub="$hargaSub" tone="amber" icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>' />
        <x-kpi-card :title="__('bahan_dasar.kpi_total_batches')" :value="$batchValue" :sub="$batchSub" tone="blue" icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>' />
        <x-kpi-card :title="__('bahan_dasar.kpi_total_value')" :value="$nilaiValue" :sub="$nilaiSub" tone="violet" icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>' />
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header border-b border-slate-100 pb-4">
            <h2 class="text-base font-extrabold text-slate-900">{{ __('bahan_dasar.section_detail_info') }}</h2>
        </div>
        <div class="bakery-card-body">
            <dl class="grid gap-x-8 gap-y-0 sm:grid-cols-2">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('app.common.id') }}</dt>
                    <dd class="font-bold text-slate-800">{{ $item->id }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('app.common.name') }}</dt>
                    <dd class="font-semibold text-slate-800">{{ $item->nama }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('app.common.unit') }}</dt>
                    <dd class="font-semibold uppercase text-slate-800">{{ $satuan }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('stock.field_min') }}</dt>
                    <dd><x-unit-qty :qty="$item->min" :unit="$satuan" qty-class="font-semibold text-slate-800" /></dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3 text-sm sm:col-span-2">
                    <dt class="text-slate-400">{{ __('app.common.status') }}</dt>
                    <dd><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $statusBadgeClass }}">{{ $statusLabel }}</span></dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header bakery-card-header--bordered">
            <h2 class="text-base font-extrabold text-slate-900">{{ __('bahan_dasar.section_batches') }}</h2>
            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">{{ __('app.common.total') }} {{ FormatHelper::formatQtyOne($item->jumlah) }} {{ $satuan }}</span>
        </div>
        <div class="bakery-card-body overflow-x-auto pt-2">
            @if ($batches->isNotEmpty())
                <table class="bakery-table w-full min-w-[40rem]">
                    <thead>
                        <tr>
                            <th class="w-[7rem]">{{ __('app.common.date') }}</th>
                            <th class="w-[7rem]">{{ __('stock.label_in') }}</th>
                            <th class="w-[7rem]">{{ __('stock.label_remaining') }}</th>
                            <th class="w-[8rem]">{{ __('bahan_dasar.label_total_cost') }}</th>
                            <th>{{ __('app.common.note') }}</th>
                            <th class="w-[4rem] text-center">{{ __('app.common.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batches as $batch)
                            <tr>
                                <td class="whitespace-nowrap text-slate-700">{{ FormatHelper::dateId($batch->tanggal) }}</td>
                                <td class="font-semibold text-slate-800">{{ FormatHelper::formatQtyOne($batch->jumlah) }} {{ $satuan }}</td>
                                <td class="font-bold text-emerald-700">{{ FormatHelper::formatQtyOne($batch->sisa) }} {{ $satuan }}</td>
                                <td class="text-slate-700">{{ FormatHelper::rupiah($batch->total_biaya) }}</td>
                                <td class="max-w-[12rem] truncate text-slate-500" title="{{ $batch->catatan }}">{{ $batch->catatan ?: '—' }}</td>
                                <td class="text-center">
                                    @if ((float) $batch->sisa >= (float) $batch->jumlah - 0.000_1)
                                        <form method="POST" action="{{ route($destroyBatchRoute, [$item->id, $batch->id]) }}" class="inline" onsubmit="return confirm('{{ __('bahan_dasar.confirm_delete_batch') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600" title="{{ __('bahan_dasar.delete_batch') }}" aria-label="{{ __('bahan_dasar.delete_batch') }}">
                                                <x-icons.trash class="h-4 w-4" />
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] font-semibold text-slate-400">{{ __('bahan_dasar.label_used') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="py-10 text-center">
                    <p class="text-sm font-semibold text-slate-500">{{ __('bahan_dasar.empty_no_dough') }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header bakery-card-header--bordered">
            <h2 class="text-base font-extrabold text-slate-900">{{ __('bahan_dasar.section_used_in_production') }}</h2>
            <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-bold text-sky-700">{{ ($produksiTerpakai ?? collect())->count() }} produksi</span>
        </div>
        <div class="bakery-card-body overflow-x-auto pt-2">
            @if (($produksiTerpakai ?? collect())->isNotEmpty())
                <table class="bakery-table w-full min-w-[44rem]">
                    <thead>
                        <tr>
                            <th class="w-[90px]">{{ __('app.common.id') }}</th>
                            <th class="w-[7rem]">{{ __('app.common.date') }}</th>
                            <th>{{ __('stock.label_product') }}</th>
                            <th class="w-[7rem] text-center">{{ __('app.common.status') }}</th>
                            <th class="min-w-[8.5rem]">{{ __('stock.label_dose') }}</th>
                            <th class="w-[7rem] text-right">{{ __('app.common.price') }}</th>
                            <th class="w-[4rem] text-center">{{ __('app.common.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produksiTerpakai as $entry)
                            @php
                                $record = $entry->record;
                                $statusClass = $record->status === 'Berhasil'
                                    ? 'bg-emerald-50 text-emerald-600'
                                    : 'bg-rose-50 text-rose-600';
                            @endphp
                            <tr>
                                <td class="font-bold text-slate-800">{{ $record->id }}</td>
                                <td class="whitespace-nowrap text-slate-700">{{ FormatHelper::dateId($record->tanggal) }}</td>
                                <td class="font-semibold text-slate-800">{{ $record->product_name }}</td>
                                <td class="text-center">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ LocaleLabels::productionStatus($record->status) }}</span>
                                </td>
                                <td><x-unit-qty :qty="$entry->total_qty" :unit="$entry->satuan" qty-class="font-semibold text-slate-800" anchor-toggle /></td>
                                <td class="text-right font-bold text-slate-800">{{ FormatHelper::rupiah($entry->total_biaya) }}</td>
                                <td class="text-center">
                                    <a
                                        href="{{ route('admin.produksi.show', $record->id) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-sky-50 hover:text-sky-600"
                                        title="{{ __('bahan_dasar.production_detail') }}"
                                        aria-label="{{ __('bahan_dasar.production_detail') }}"
                                    >
                                        <x-icons.info-circle class="h-4 w-4" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="py-10 text-center">
                    <p class="text-sm font-semibold text-slate-500">{{ __('bahan_dasar.empty_not_used') }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header bakery-card-header--bordered">
            <h2 class="text-base font-extrabold text-slate-900">{{ __('bahan_dasar.section_raw_history') }}</h2>
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ $allPemakaian->count() }} baris</span>
        </div>
        <div class="bakery-card-body overflow-x-auto pt-2">
            @if ($allPemakaian->isNotEmpty())
                <table class="bakery-table w-full min-w-[48rem]">
                    <thead>
                        <tr>
                            <th class="w-[7rem]">{{ __('app.common.date') }}</th>
                            <th class="w-[8rem]">{{ __('bahan_dasar.label_dough_batch') }}</th>
                            <th>{{ __('bahan_dasar.label_raw_material') }}</th>
                            <th class="min-w-[8.5rem]">{{ __('stock.label_dose') }}</th>
                            <th class="w-[7rem] text-right">{{ __('app.common.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batches as $batch)
                            @foreach ($batch->pemakaianBahanBaku as $usage)
                                <tr>
                                    <td class="whitespace-nowrap text-slate-700">{{ FormatHelper::dateId($batch->tanggal) }}</td>
                                    <td class="text-sm font-semibold text-slate-700">#{{ $batch->id }}</td>
                                    <td class="font-semibold text-slate-800">{{ $usage->bahanBaku?->nama ?? $usage->raw_material_id }}</td>
                                    <td><x-unit-qty :qty="$usage->jumlah" :unit="$usage->satuan" qty-class="font-semibold text-slate-800" anchor-toggle /></td>
                                    <td class="text-right font-bold text-slate-800">{{ FormatHelper::rupiah($usage->total) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-amber-50/60">
                            <td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-amber-900">{{ __('bahan_dasar.footer_raw_cost') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-extrabold text-amber-800">{{ FormatHelper::rupiah($totalBiayaBatch) }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="py-10 text-center">
                    <p class="text-sm font-semibold text-slate-500">{{ __('bahan_dasar.empty_no_raw_usage') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

@include('partials.bahan-dasar-action-modals')
