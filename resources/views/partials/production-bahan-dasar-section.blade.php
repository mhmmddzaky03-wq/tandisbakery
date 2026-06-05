@php
    use App\Support\UnitConverter;

    $initialRows = $initialRows ?? [];
    if (! is_array($initialRows)) {
        $initialRows = [];
    }

    $itemsJson = $bahanDasarItems->map(function ($item) {
        $satuan = $item->satuan ?? 'g';
        $batches = $item->batches
            ->filter(fn ($b) => (float) $b->sisa > 0)
            ->map(fn ($b) => [
                'id' => $b->id,
                'label' => $b->displayLabel($satuan),
                'sisa' => (float) $b->sisa,
            ])
            ->values();

        return [
            'id' => $item->id,
            'nama' => $item->nama,
            'jumlah' => (float) $item->jumlah,
            'satuan' => $satuan,
            'alternatives' => UnitConverter::alternatives($satuan),
            'batches' => $batches,
        ];
    })->values();

    $unitShortLabels = ['g' => 'g', 'gram' => 'gr', 'kg' => 'kg'];
@endphp

<div
    data-production-bahan-dasar
    data-select-placeholder="{{ __('production.base_select') }}"
    data-batch-placeholder="{{ __('production.base_batch_select') }}"
>
    <script type="application/json" data-bahan-dasar-json>@json($itemsJson)</script>
    <script type="application/json" data-bahan-dasar-initial-rows>@json($initialRows)</script>
    <script type="application/json" data-bahan-dasar-unit-labels>@json($unitShortLabels)</script>

    @if ($bahanDasarItems->isEmpty())
        <div class="rounded-xl border border-dashed border-violet-200 bg-violet-50/40 px-4 py-6 text-center">
            <p class="text-sm font-semibold text-violet-800">{{ __('production.base_empty') }}</p>
        </div>
    @else
        <div class="overflow-hidden rounded-xl ring-1 ring-violet-200/80">
            <div class="hidden border-b border-violet-100 bg-violet-50/80 px-3 py-2.5 text-[11px] font-bold uppercase tracking-wide text-violet-600 sm:grid sm:grid-cols-[minmax(11rem,1fr)_8.5rem_9rem_2rem] sm:gap-2.5 sm:px-4">
                <span>{{ __('production.base_header') }}</span>
                <span>{{ __('production.label_dose') }}</span>
                <span class="text-right">{{ __('app.common.stock') }}</span>
                <span class="sr-only">{{ __('app.common.action') }}</span>
            </div>
            <div class="divide-y divide-violet-100 bg-white" data-bahan-dasar-rows></div>
            <button
                type="button"
                class="flex w-full items-center justify-center gap-2 border-t border-dashed border-violet-200 bg-violet-50/40 px-4 py-3 text-sm font-bold text-violet-600 transition hover:bg-violet-50 hover:text-violet-800"
                data-bahan-dasar-add-row
            >
                <x-icons.plus class="h-4 w-4" />
                {{ __('production.base_add') }}
            </button>
        </div>
        @error('bahan_dasar')
            <p class="mt-2 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
        @enderror

        <template data-bahan-dasar-row-template>
            <div class="group px-3 py-3 transition sm:grid sm:grid-cols-[minmax(11rem,1fr)_8.5rem_9rem_2rem] sm:items-center sm:gap-2.5 sm:px-4 sm:py-2.5" data-bahan-dasar-row>
                <div class="min-w-0 space-y-1.5">
                    <select name="bahan_dasar[__INDEX__][bahan_dasar_id]" class="bakery-input h-10 w-full min-w-0 text-sm" data-bd-select data-bd-required>
                        <option value="" disabled selected>{{ __('production.base_select') }}</option>
                    </select>
                    <select class="bakery-input hidden h-9 w-full min-w-0 text-xs" data-bd-batch-select>
                        <option value="" disabled selected>{{ __('production.base_batch_select') }}</option>
                    </select>
                    <input type="hidden" value="" data-bd-batch-hidden disabled />
                </div>
                <div class="mt-2 sm:mt-0">
                    <div class="unit-input-group h-10" data-bd-qty-group>
                        <input type="text" name="bahan_dasar[__INDEX__][jumlah]" inputmode="decimal" data-decimal-one data-bd-qty disabled class="disabled:cursor-not-allowed disabled:text-slate-400" />
                        <input type="hidden" name="bahan_dasar[__INDEX__][satuan]" value="" data-bd-unit-input />
                        <span class="inline-flex h-10 shrink-0 items-center rounded-lg bg-violet-50 px-2.5 text-[11px] font-bold uppercase text-violet-700" data-bd-unit-static>—</span>
                    </div>
                </div>
                <div class="mt-2 sm:mt-0">
                    <div class="unit-stock-card unit-stock-card--production">
                        <div class="unit-stock-card__qty-row">
                            <span data-bd-stock-qty class="text-sm font-bold tabular-nums text-slate-800">—</span>
                            <span class="shrink-0 text-[11px] font-bold uppercase text-violet-600" data-bd-stock-unit>—</span>
                        </div>
                        <div class="unit-stock-card__remain text-[10px] leading-tight text-slate-500">
                            {{ __('production.label_remaining') }} <span data-bd-remain-qty class="font-bold tabular-nums text-emerald-600">—</span>
                        </div>
                    </div>
                </div>
                <div class="mt-2 flex justify-end sm:mt-0 sm:justify-center">
                    <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" data-bahan-dasar-remove-row title="{{ __('app.common.delete') }}" aria-label="{{ __('app.common.delete') }}">
                        <x-icons.trash class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </template>
    @endif
</div>
