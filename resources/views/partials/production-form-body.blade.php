@php
    $prefix = $prefix ?? 'create';
    $isCreateForm = $prefix === 'create';
    $catalogProductNames = $catalogProductNames ?? collect();
    $useExistingProduct = old('use_existing_product') === '1';
    $tanggalValue = old('tanggal', $tanggal ?? date('Y-m-d'));
    $productNameValue = old('product_name', $productName ?? '');
    $jumlahValue = old('jumlah', $jumlah ?? '');
    $statusValue = old('status', $status ?? 'Berhasil');
    $notesValue = old('notes', $notes ?? '');
    $materialRows = $materialRows ?? ($materialsOptional ? [] : [['raw_material_id' => '', 'jumlah' => '', 'satuan' => '']]);
    $bahanDasarRows = $bahanDasarRows ?? [];
    $autofocus = $autofocus ?? true;
    $linkedProductId = $linkedProductId ?? null;
    $useBahanDasar = old('use_bahan_dasar') !== null
        ? old('use_bahan_dasar') == '1'
        : ($useBahanDasar ?? count(array_filter($bahanDasarRows, fn ($r) => ! empty($r['bahan_dasar_id']))) > 0);
    $materialsOptional = $useBahanDasar;
    $showFormErrors = $errors->any() && (
        ($prefix === 'create' && ! old('_edit_id'))
        || (old('_edit_id') === (string) $prefix)
    );
@endphp

<div class="space-y-5">
    {{-- Section 1: Informasi produksi --}}
    <section>
        <div class="mb-3 flex items-center gap-2.5">
            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-sky-100 text-xs font-extrabold text-sky-700">1</span>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Informasi Produksi</h3>
                <p class="text-xs text-slate-500">Tanggal, produk, jumlah hasil, dan status batch</p>
            </div>
        </div>

        <div class="space-y-4 rounded-xl bg-slate-50/70 p-4 ring-1 ring-slate-100">
            @if ($showFormErrors)
                <div class="rounded-xl bg-rose-50 px-4 py-3 ring-1 ring-rose-200" role="alert" data-form-error-banner>
                    <p class="text-sm font-extrabold text-rose-800">Data gagal disimpan. Periksa isian berikut:</p>
                    <ul class="mt-1.5 space-y-0.5 text-xs font-semibold text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="min-w-0 flex-1" data-production-product-field>
                    <div class="mb-1.5 flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                        <label for="field-product-{{ $prefix }}" class="text-xs font-bold text-slate-600">
                            Nama Produk
                            <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        @if ($isCreateForm && $catalogProductNames->isNotEmpty())
                            <label class="inline-flex cursor-pointer items-center gap-1.5 text-[11px] font-semibold text-slate-500">
                                <input
                                    type="checkbox"
                                    name="use_existing_product"
                                    value="1"
                                    class="h-3.5 w-3.5 shrink-0 rounded border-slate-300 text-sky-600 focus:ring-sky-400"
                                    data-production-use-catalog
                                    @checked($useExistingProduct)
                                />
                                Sudah ada di data produk
                            </label>
                        @endif
                    </div>
                    <input
                        id="field-product-{{ $prefix }}"
                        type="text"
                        value="{{ $productNameValue }}"
                        placeholder="Contoh: Roti Tawar"
                        data-title-case
                        data-production-product-text
                        @if (! $useExistingProduct || ! $isCreateForm) name="product_name" @endif
                        @if (! $useExistingProduct) required @endif
                        @if ($useExistingProduct && $isCreateForm) disabled @endif
                        @if ($autofocus && ! $useExistingProduct) autofocus @endif
                        class="bakery-input h-11 w-full {{ ($useExistingProduct && $isCreateForm) ? 'hidden' : '' }} {{ $errors->has('product_name') ? '!ring-2 !ring-rose-400' : '' }}"
                    />
                    @if ($isCreateForm && $catalogProductNames->isNotEmpty())
                        <select
                            id="field-product-select-{{ $prefix }}"
                            data-production-product-select
                            @if ($useExistingProduct) name="product_name" required @endif
                            @if (! $useExistingProduct) disabled @endif
                            @if ($autofocus && $useExistingProduct) autofocus @endif
                            class="bakery-input h-11 w-full {{ $useExistingProduct ? '' : 'hidden' }} {{ $errors->has('product_name') ? '!ring-2 !ring-rose-400' : '' }}"
                        >
                            <option value="" disabled @selected($productNameValue === '')>Pilih produk</option>
                            @foreach ($catalogProductNames as $name)
                                <option value="{{ $name }}" @selected($productNameValue === $name)>{{ $name }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('product_name')
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div class="min-w-0 flex-1">
                    <label for="field-tanggal-{{ $prefix }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                        Tanggal
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="field-tanggal-{{ $prefix }}"
                        name="tanggal"
                        type="date"
                        value="{{ $tanggalValue }}"
                        required
                        class="bakery-input h-11 w-full {{ $errors->has('tanggal') ? '!ring-2 !ring-rose-400' : '' }}"
                    />
                    @error('tanggal')
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="min-w-0 flex-1">
                    <label for="field-jumlah-{{ $prefix }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                        Jumlah Hasil
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </label>
                    <div class="flex h-11 items-center gap-2">
                        <input
                            id="field-jumlah-{{ $prefix }}"
                            name="jumlah"
                            type="text"
                            value="{{ $jumlahValue }}"
                            required
                            inputmode="numeric"
                            data-integer-only
                            class="bakery-input h-11 min-w-0 flex-1 {{ $errors->has('jumlah') ? '!ring-2 !ring-rose-400' : '' }}"
                        />
                        <span class="inline-flex h-11 w-12 shrink-0 items-center justify-center rounded-lg bg-slate-200/80 text-xs font-bold uppercase text-slate-600">pcs</span>
                    </div>
                    @error('jumlah')
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div class="min-w-0 flex-1">
                    <span class="mb-1.5 block text-xs font-bold text-slate-600">
                        Status
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </span>
                    <div class="grid h-11 grid-cols-2 gap-2" role="radiogroup" aria-label="Status">
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="status"
                                value="Berhasil"
                                class="peer sr-only"
                                @checked($statusValue === 'Berhasil')
                                required
                            />
                            <span class="flex h-11 items-center justify-center gap-1.5 rounded-xl bg-white text-sm font-bold text-slate-600 ring-1 ring-slate-200 transition peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-checked:ring-2 peer-checked:ring-emerald-400">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5"/></svg>
                                Berhasil
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="status"
                                value="Gagal"
                                class="peer sr-only"
                                @checked($statusValue === 'Gagal')
                            />
                            <span class="flex h-11 items-center justify-center gap-1.5 rounded-xl bg-white text-sm font-bold text-slate-600 ring-1 ring-slate-200 transition peer-checked:bg-rose-50 peer-checked:text-rose-700 peer-checked:ring-2 peer-checked:ring-rose-400">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
                                Gagal
                            </span>
                        </label>
                    </div>
                    @error('status')
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <input type="hidden" name="satuan" value="pcs" />
        </div>
    </section>

    {{-- Toggle & pemakaian bahan dasar --}}
    <section data-production-bahan-dasar-wrap>
        <div class="mb-3 flex flex-wrap items-start gap-2.5">
            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-violet-100 text-xs font-extrabold text-violet-700">2</span>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                    <h3 class="text-sm font-extrabold text-slate-900">Pemakaian Bahan Dasar</h3>
                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">Opsional</span>
                </div>
                <p class="mt-0.5 text-xs text-slate-500">Centang jika produk memakai adonan setengah jadi dari menu Bahan Dasar.</p>
            </div>
        </div>

        <label class="mb-3 flex cursor-pointer items-center gap-3 rounded-xl bg-violet-50/60 px-4 py-3 ring-1 ring-violet-100 transition hover:bg-violet-50">
            <input
                type="checkbox"
                name="use_bahan_dasar"
                value="1"
                class="h-4 w-4 shrink-0 rounded border-violet-300 text-violet-600 focus:ring-violet-400"
                data-production-use-bahan-dasar
                @checked($useBahanDasar)
            />
            <span class="text-sm font-semibold text-violet-900">Produksi ini memakai bahan dasar (adonan)</span>
        </label>

        <div
            data-production-bahan-dasar-panel
            class="{{ $useBahanDasar ? '' : 'hidden' }}"
        >
            @include('partials.production-bahan-dasar-section', [
                'bahanDasarItems' => $bahanDasarItems ?? collect(),
                'initialRows' => $bahanDasarRows,
            ])
        </div>
    </section>

    {{-- Pemakaian bahan baku --}}
    <section>
        <div class="mb-3 flex items-center gap-2.5">
            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-amber-100 text-xs font-extrabold text-amber-700">3</span>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                    <h3 class="text-sm font-extrabold text-slate-900">Pemakaian Bahan Baku</h3>
                    <span
                        data-production-materials-badge
                        class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $materialsOptional ? 'bg-violet-50 text-violet-700' : 'bg-rose-50 text-rose-600' }}"
                    >{{ $materialsOptional ? 'Opsional' : 'Wajib' }}</span>
                </div>
                <p class="mt-0.5 text-xs text-slate-500" data-production-materials-hint>
                    @if ($materialsOptional)
                        Bahan baku langsung dari stok. Tidak wajib jika sudah memakai bahan dasar.
                    @else
                        Bahan baku langsung dari stok. Wajib diisi jika produksi tidak memakai bahan dasar.
                    @endif
                </p>
            </div>
        </div>

        @include('partials.production-material-section', [
            'materials' => $materials,
            'initialRows' => $materialRows,
            'optional' => $materialsOptional,
        ])
    </section>

    {{-- Keterangan --}}
    <section>
        <div class="mb-3 flex items-center gap-2.5">
            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-slate-200 text-xs font-extrabold text-slate-600">4</span>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Keterangan</h3>
                <p class="text-xs text-slate-500">Opsional — alasan jika produksi gagal</p>
            </div>
        </div>

        <div class="bakery-field">
            <textarea
                id="field-notes-{{ $prefix }}"
                name="notes"
                rows="2"
                placeholder="Opsional — catatan tambahan atau alasan jika gagal"
                class="bakery-input min-h-[72px] w-full resize-y {{ $errors->has('notes') ? '!ring-2 !ring-rose-400' : '' }}"
            >{{ $notesValue }}</textarea>
            @error('notes')
                <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
            @enderror
        </div>
    </section>

    @if ($linkedProductId)
        <p class="rounded-xl bg-sky-50 px-4 py-3 text-xs font-semibold text-sky-700">
            Produksi ini terdaftar sebagai sumber awal produk katalog {{ $linkedProductId }}.
        </p>
    @endif
</div>
