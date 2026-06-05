@php
    use App\Support\FormatHelper;

    $stockValue = number_format($product->jumlah, 0, ',', '.').' '.$product->satuan;
    $stockSub = __('product.kpi_stock_sub', ['count' => $productionBatchCount]);
    $hargaValue = FormatHelper::rupiah($product->harga);
    $hargaSub = __('product.kpi_selling_price_sub');
    $nilaiStok = (int) $product->jumlah * (int) $product->harga;
    $nilaiStokValue = FormatHelper::rupiah($nilaiStok);
    $nilaiStokSub = __('product.kpi_stock_value_sub');
    $batchValue = (string) $productionBatchCount;
    $batchSub = __('product.kpi_batches_sub');
    $productionShowRoute = $productionShowRoute ?? 'admin.produksi.show';
@endphp

<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi-card
            :title="__('product.kpi_current_stock')"
            :value="$stockValue"
            :sub="$stockSub"
            tone="green"
            icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>'
        />
        <x-kpi-card
            :title="__('product.kpi_selling_price')"
            :value="$hargaValue"
            :sub="$hargaSub"
            tone="amber"
            icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'
        />
        <x-kpi-card
            :title="__('product.kpi_stock_value')"
            :value="$nilaiStokValue"
            :sub="$nilaiStokSub"
            tone="violet"
            icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>'
        />
        <x-kpi-card
            :title="__('product.kpi_batches')"
            :value="$batchValue"
            :sub="$batchSub"
            tone="blue"
            icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>'
        />
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header border-b border-slate-100 pb-4">
            <h2 class="text-base font-extrabold text-slate-900">{{ __('product.section_info') }}</h2>
        </div>
        <div class="bakery-card-body">
            <dl class="grid gap-x-8 gap-y-0 sm:grid-cols-2">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('product.label_product_id') }}</dt>
                    <dd class="font-bold text-slate-800">{{ $product->id }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('product.field_product_name') }}</dt>
                    <dd class="text-right font-semibold text-slate-800">{{ $product->nama }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('product.field_unit') }}</dt>
                    <dd class="font-semibold uppercase text-slate-800">{{ $product->satuan }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">{{ __('product.field_selling_price') }}</dt>
                    <dd class="font-extrabold text-amber-600">{{ FormatHelper::rupiah($product->harga) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm sm:border-b-0">
                    <dt class="text-slate-400">{{ __('product.label_initial_production') }}</dt>
                    <dd class="font-semibold text-slate-800">
                        @if ($product->productionRecord)
                            <a href="{{ route($productionShowRoute, $product->productionRecord->id) }}" class="hover:text-sky-600">
                                {{ $product->productionRecord->id }}
                            </a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-start gap-3 py-3 text-sm sm:col-span-2">
                    <dt class="shrink-0 text-slate-400">{{ __('product.label_note') }}</dt>
                    <dd class="text-slate-600">{{ __('product.note_auto_stock') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header bakery-card-header--bordered">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">{{ __('product.section_success_history') }}</h2>
            </div>
            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
                {{ __('product.catalog_total_qty', ['qty' => number_format($product->jumlah, 0, ',', '.').' '.$product->satuan]) }}
            </span>
        </div>
        <div class="bakery-card-body overflow-x-auto pt-2">
            @if ($productions->isNotEmpty())
                <table class="bakery-table w-full min-w-[36rem]">
                    <thead>
                        <tr>
                            <th class="w-[7rem]">{{ __('app.common.date') }}</th>
                            <th class="w-[6rem]">{{ __('production.label_prod_id') }}</th>
                            <th class="w-[8rem]">{{ __('product.label_contribution') }}</th>
                            <th class="w-[5rem] text-center">{{ __('app.common.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productions as $production)
                            <tr>
                                <td class="whitespace-nowrap text-slate-700">{{ FormatHelper::dateId($production->tanggal) }}</td>
                                <td class="font-bold text-slate-800">
                                    <a href="{{ route($productionShowRoute, $production->id) }}" class="hover:text-sky-600">
                                        {{ $production->id }}
                                    </a>
                                </td>
                                <td class="font-semibold text-emerald-700">
                                    +{{ number_format($production->jumlah, 0, ',', '.') }} {{ $production->satuan }}
                                </td>
                                <td class="text-center">
                                    <a
                                        href="{{ route($productionShowRoute, $production->id) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-sky-50 hover:text-sky-600"
                                        title="{{ __('product.detail_production') }}"
                                        aria-label="{{ __('product.detail_production') }}"
                                    >
                                        <x-icons.info-circle class="h-4 w-4" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-emerald-50/60">
                            <td colspan="2" class="px-4 py-3 text-right text-sm font-bold text-emerald-900">{{ __('product.footer_catalog_total') }}</td>
                            <td colspan="2" class="px-4 py-3 text-sm font-extrabold text-emerald-800">
                                {{ number_format($product->jumlah, 0, ',', '.') }} {{ $product->satuan }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="py-10 text-center">
                    <p class="text-sm font-semibold text-slate-500">{{ __('product.empty_success_production') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
