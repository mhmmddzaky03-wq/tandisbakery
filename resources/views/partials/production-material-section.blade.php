@php
    $initialRows = $initialRows ?? [['raw_material_id' => '', 'jumlah' => '']];
    if (count($initialRows) === 0) {
        $initialRows = [['raw_material_id' => '', 'jumlah' => '']];
    }
    $materialsJson = $materials->map(fn ($m) => [
        'id' => $m->id,
        'nama' => $m->nama,
        'jumlah' => (float) $m->jumlah,
        'satuan' => $m->satuan ?? 'kg',
    ])->values();
@endphp

<div
    data-production-materials
    data-materials='@json($materialsJson)'
    data-initial-rows='@json($initialRows)'
    data-select-placeholder="Pilih bahan baku"
>
    @if ($materials->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 px-4 py-8 text-center">
            <p class="text-sm font-semibold text-slate-600">Belum ada bahan baku terdaftar. Tambahkan bahan baku di menu Stok terlebih dahulu.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-xl ring-1 ring-slate-200">
            {{-- Table header (desktop) --}}
            <div class="hidden border-b border-slate-100 bg-slate-50/90 px-3 py-2.5 text-[11px] font-bold uppercase tracking-wide text-slate-500 sm:grid sm:grid-cols-[minmax(0,1fr)_9rem_7.5rem_2rem] sm:gap-3 sm:px-4">
                <span>Bahan Baku</span>
                <span>Takaran</span>
                <span class="text-right">Stok tersedia</span>
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
            <div class="group px-3 py-3 transition sm:grid sm:grid-cols-[minmax(0,1fr)_9rem_7.5rem_2rem] sm:items-center sm:gap-3 sm:px-4 sm:py-2.5" data-production-row>
                {{-- Bahan baku --}}
                <div class="min-w-0">
                    <label class="mb-1 block text-[11px] font-bold text-slate-500 sm:sr-only">Bahan Baku</label>
                    <select
                        name="materials[__INDEX__][raw_material_id]"
                        class="bakery-input h-10 w-full text-sm"
                        data-material-select
                        required
                    >
                        <option value="">Pilih bahan baku</option>
                    </select>
                </div>

                {{-- Takaran --}}
                <div class="mt-2 sm:mt-0">
                    <label class="mb-1 block text-[11px] font-bold text-slate-500 sm:sr-only">Takaran</label>
                    <div class="flex items-center gap-1.5">
                        <input
                            type="text"
                            name="materials[__INDEX__][jumlah]"
                            inputmode="decimal"
                            autocomplete="off"
                            data-decimal-one
                            data-material-qty
                            disabled
                            value="0"
                            placeholder="0"
                            class="bakery-input h-10 min-w-0 flex-1 text-sm disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
                        />
                        <span
                            data-material-unit
                            class="inline-flex h-10 min-w-[2.75rem] shrink-0 items-center justify-center rounded-lg bg-slate-100 px-1.5 text-[11px] font-bold uppercase text-slate-600"
                        >—</span>
                    </div>
                </div>

                {{-- Stok --}}
                <div class="mt-2 flex items-center justify-between gap-2 sm:mt-0 sm:justify-end">
                    <span class="text-[11px] font-bold text-slate-400 sm:hidden">Stok tersedia</span>
                    <div class="text-right text-[11px] leading-tight">
                        <div class="text-slate-500">
                            <span data-material-stock class="font-bold text-slate-700">—</span>
                        </div>
                        <div class="mt-0.5 text-slate-400">
                            Sisa stok:
                            <span data-material-remain class="font-bold text-emerald-600">—</span>
                        </div>
                    </div>
                </div>

                {{-- Hapus --}}
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
