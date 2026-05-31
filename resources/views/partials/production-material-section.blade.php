@php
    use App\Support\UnitConverter;

    $initialRows = $initialRows ?? [];
    if (! is_array($initialRows)) {
        $initialRows = [];
    }
    $optional = $optional ?? false;
    $materialsJson = $materials->map(function ($m) {
        $satuan = $m->satuan ?? 'kg';
        $batches = $m->restocks
            ->filter(fn ($r) => (float) $r->sisa > 0)
            ->map(fn ($r) => [
                'id' => $r->id,
                'label' => $r->displayLabel($satuan),
                'sisa' => (float) $r->sisa,
            ])
            ->values();

        return [
            'id' => $m->id,
            'nama' => $m->nama,
            'jumlah' => (float) $m->jumlah,
            'satuan' => $satuan,
            'alternatives' => UnitConverter::alternatives($satuan),
            'batches' => $batches,
        ];
    })->values();
    $unitShortLabels = [
        'kg' => 'kg',
        'gram' => 'gr',
        'L' => 'L',
        'ml' => 'mL',
    ];
@endphp

<div
    data-production-materials
    data-select-placeholder="Pilih bahan baku"
    data-batch-placeholder="Pilih batch stok"
    @if ($optional) data-materials-optional="true" @endif
>
    <script type="application/json" data-production-materials-json>@json($materialsJson)</script>
    <script type="application/json" data-production-initial-rows>@json($initialRows)</script>
    <script type="application/json" data-production-unit-labels>@json($unitShortLabels)</script>
    @if ($materials->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 px-4 py-8 text-center">
            <p class="text-sm font-semibold text-slate-600">Belum ada bahan baku terdaftar. Tambahkan bahan baku di menu Stok terlebih dahulu.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-xl ring-1 ring-slate-200">
            <div class="hidden border-b border-slate-100 bg-slate-50/90 px-3 py-2.5 text-[11px] font-bold uppercase tracking-wide text-slate-500 sm:grid sm:grid-cols-[minmax(11rem,1fr)_8.5rem_9rem_2rem] sm:gap-2.5 sm:px-4">
                <span>Bahan / Batch</span>
                <span>Takaran</span>
                <span class="text-right">Stok</span>
                <span class="sr-only">Aksi</span>
            </div>

            <div class="divide-y divide-slate-100 bg-white" data-production-rows></div>

            <button
                type="button"
                class="flex w-full items-center justify-center gap-2 border-t border-dashed border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-bold text-slate-500 transition hover:bg-sky-50/60 hover:text-sky-700"
                data-production-add-row
            >
                <x-icons.plus class="h-4 w-4" />
                Tambah bahan
            </button>
        </div>

        @error('materials')
            <p class="mt-2 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
        @enderror

        <template data-production-row-template>
            <div class="group px-3 py-3 transition sm:grid sm:grid-cols-[minmax(11rem,1fr)_8.5rem_9rem_2rem] sm:items-center sm:gap-2.5 sm:px-4 sm:py-2.5" data-production-row>
                <div class="min-w-0 space-y-1.5">
                    <label class="mb-1 block text-[11px] font-bold text-slate-500 sm:sr-only">Bahan Baku</label>
                    <select
                        name="materials[__INDEX__][raw_material_id]"
                        class="bakery-input h-10 w-full min-w-0 text-sm"
                        data-material-select
                        @unless ($optional) required @endunless
                    >
                        <option value="" disabled selected>Pilih bahan baku</option>
                    </select>
                    <select
                        class="bakery-input hidden h-9 w-full min-w-0 text-xs"
                        data-material-batch-select
                    >
                        <option value="" disabled selected>Pilih batch stok</option>
                    </select>
                    <input type="hidden" value="" data-material-batch-hidden disabled />
                </div>

                <div class="mt-2 sm:mt-0">
                    <label class="mb-1 block text-[11px] font-bold text-slate-500 sm:sr-only">Takaran</label>
                    <div class="unit-input-group h-10" data-material-qty-group>
                        <input
                            type="text"
                            name="materials[__INDEX__][jumlah]"
                            inputmode="decimal"
                            autocomplete="off"
                            data-decimal-one
                            data-material-qty
                            disabled
                            class="disabled:cursor-not-allowed disabled:text-slate-400"
                        />
                        <div class="unit-input-group__units hidden" data-material-unit-wrap>
                            <input type="hidden" name="materials[__INDEX__][satuan]" value="" data-unit-toggle-input data-material-unit-input />
                            <div class="unit-toggle unit-toggle--compact" data-unit-toggle data-material-unit-toggle role="group" aria-label="Satuan takaran"></div>
                        </div>
                        <span class="hidden shrink-0 px-2 text-[11px] font-bold uppercase text-slate-500" data-material-unit-static>—</span>
                    </div>
                </div>

                <div class="mt-2 sm:mt-0">
                    <span class="mb-1 block text-[11px] font-bold text-slate-400 sm:sr-only">Stok</span>
                    <div class="unit-stock-card unit-stock-card--production">
                        <div class="unit-stock-card__qty-row">
                            <span data-material-stock-qty class="text-sm font-bold tabular-nums text-slate-800">—</span>
                            <div class="unit-stock-card__unit-slot">
                                <div class="hidden shrink-0" data-material-stock-unit-wrap>
                                    <div class="unit-toggle unit-toggle--compact" data-unit-toggle data-material-stock-unit-toggle role="group" aria-label="Satuan stok"></div>
                                </div>
                                <span class="hidden shrink-0 text-[11px] font-bold uppercase text-slate-500" data-material-stock-unit-static>—</span>
                            </div>
                        </div>
                        <div class="unit-stock-card__remain text-[10px] leading-tight text-slate-500">
                            Sisa <span data-material-remain-qty class="font-bold tabular-nums text-emerald-600">—</span>
                        </div>
                    </div>
                </div>

                <div class="mt-2 flex justify-end sm:mt-0 sm:justify-center">
                    <button
                        type="button"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 sm:opacity-60 sm:group-hover:opacity-100"
                        data-production-remove-row
                        title="Hapus"
                        aria-label="Hapus"
                    >
                        <x-icons.trash class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </template>
    @endif
</div>
