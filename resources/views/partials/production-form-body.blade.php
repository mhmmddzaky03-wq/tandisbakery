@php
    $prefix = $prefix ?? 'create';
    $tanggalValue = old('tanggal', $tanggal ?? date('Y-m-d'));
    $productNameValue = old('product_name', $productName ?? '');
    $jumlahValue = old('jumlah', $jumlah ?? '');
    $statusValue = old('status', $status ?? 'Berhasil');
    $notesValue = old('notes', $notes ?? '');
    $materialRows = $materialRows ?? [['raw_material_id' => '', 'jumlah' => '']];
    $autofocus = $autofocus ?? true;
    $linkedProductId = $linkedProductId ?? null;
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
            @if ($errors->any())
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
                <div class="min-w-0 flex-1">
                    <label for="field-product-{{ $prefix }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                        Nama Produk
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="field-product-{{ $prefix }}"
                        name="product_name"
                        type="text"
                        value="{{ $productNameValue }}"
                        placeholder="Contoh: Roti Tawar"
                        data-title-case
                        required
                        @if ($autofocus) autofocus @endif
                        class="bakery-input h-11 w-full {{ $errors->has('product_name') ? '!ring-2 !ring-rose-400' : '' }}"
                    />
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
                            type="number"
                            value="{{ $jumlahValue }}"
                            min="0"
                            required
                            placeholder="0"
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

    {{-- Section 2: Pemakaian bahan --}}
    <section>
        <div class="mb-3 flex items-center gap-2.5">
            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-amber-100 text-xs font-extrabold text-amber-700">2</span>
            <div class="min-w-0 flex-1">
                <h3 class="text-sm font-extrabold text-slate-900">Pemakaian Bahan Baku</h3>
                <p class="text-xs text-slate-500">Input takaran aktual per bahan untuk batch produksi ini.</p>
            </div>
        </div>

        @include('partials.production-material-section', [
            'materials' => $materials,
            'initialRows' => $materialRows,
        ])
    </section>

    {{-- Section 3: Keterangan --}}
    <section>
        <div class="mb-3 flex items-center gap-2.5">
            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-slate-200 text-xs font-extrabold text-slate-600">3</span>
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
            Produksi ini terhubung ke produk {{ $linkedProductId }}. Perubahan nama/satuan akan ikut memperbarui data produk.
        </p>
    @endif
</div>
