@php
    use App\Support\FormatHelper;
@endphp
<div>
    <div class="bakery-card" data-table-search>
        <div class="bakery-card-header flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div class="text-lg font-extrabold text-slate-900">Daftar Stok Bahan Baku</div>
            <x-table-search
                placeholder="Cari bahan baku..."
                :value="$search ?? ''"
            />
        </div>
        <div class="bakery-card-body bakery-table-wrap pt-2">
            <table class="bakery-table">
                <thead>
                    <tr>
                        <th class="w-[90px]">ID</th>
                        <th>Nama</th>
                        <th class="w-[120px]">Jumlah</th>
                        <th class="w-[160px]">Status</th>
                        <th class="w-[120px] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody data-table-search-body>
                    @forelse ($materials as $m)
                        @php
                            $jumlahStok = (float) $m->jumlah;
                            $minStok = (float) $m->min;
                            $habis = $jumlahStok <= 0;
                            $aman = ! $habis && $jumlahStok > $minStok;
                            $statusLabel = $habis
                                ? 'Stok Habis'
                                : ($aman ? 'Stok Aman' : 'Perlu Diisi');
                            $statusClass = $habis
                                ? 'bg-rose-50 text-rose-600 hover:bg-rose-100'
                                : ($aman ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-amber-50 text-amber-700 hover:bg-amber-100');
                            $satuan = $m->satuan ?? 'kg';
                            $totalNilai = (int) round((float) $m->jumlah * (int) $m->harga);
                        @endphp
                        <tr
                            data-searchable-row
                            data-search="{{ strtolower($m->id.' '.$m->nama.' '.$satuan.' '.$statusLabel) }}"
                        >
                            <td class="font-bold text-slate-800">{{ $m->id }}</td>
                            <td class="max-w-md truncate">{{ $m->nama }}</td>
                            <td>{{ FormatHelper::formatQtyOne($m->jumlah) }} {{ $satuan }}</td>
                            <td>
                                <button
                                    type="button"
                                    class="inline-flex w-[9.5rem] cursor-pointer items-center justify-between gap-2 rounded-full border-0 px-3 py-1.5 text-xs font-bold transition hover:opacity-90 {{ $statusClass }}"
                                    data-modal-open="detail-stok-{{ $m->id }}"
                                    title="Lihat detail"
                                    aria-label="Lihat detail: {{ $statusLabel }}"
                                >
                                    <span class="inline-flex min-w-0 items-center gap-1.5 truncate">
                                        @if ($habis)
                                            <x-icons.stock-empty />
                                            Stok Habis
                                        @elseif ($aman)
                                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                                            </svg>
                                            Stok Aman
                                        @else
                                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12c2-2 4-4 7-4s5 2 7 4M3 6c2-2 4-4 7-4s5 2 7 4M3 18c2-2 4-4 7-4s5 2 7 4" />
                                            </svg>
                                            Perlu Diisi
                                        @endif
                                    </span>
                                    <x-icons.info-circle class="h-3.5 w-3.5 shrink-0 opacity-80" />
                                </button>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <button
                                        type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-emerald-50 hover:text-emerald-600"
                                        data-modal-open="restock-stok-{{ $m->id }}"
                                        title="Restock"
                                        aria-label="Restock"
                                    >
                                        <x-icons.restock />
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                                        data-modal-open="edit-stok-{{ $m->id }}"
                                        title="Edit"
                                        aria-label="Edit"
                                    >
                                        <x-icons.pencil />
                                    </button>
                                    <form id="delete-stok-{{ $m->id }}" method="POST" action="{{ route($destroyRoute, $m->id) }}" class="inline">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button
                                        type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                        data-delete-confirm
                                        data-delete-form="delete-stok-{{ $m->id }}"
                                        data-confirm-message="Hapus bahan baku ini?"
                                        onclick="handleConfirmDelete(this)"
                                        title="Hapus"
                                        aria-label="Hapus"
                                    >
                                        <x-icons.trash />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr data-table-empty>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500">
                                Tidak ada bahan baku yang perlu diisi ulang
                            </td>
                        </tr>
                    @endforelse
                    <tr data-table-no-results class="hidden">
                        <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500">
                            Tidak ada data yang cocok dengan pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bakery-card mt-6" data-unit-card>
        <div class="bakery-card-header flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div class="text-lg font-extrabold text-slate-900">Daftar Satuan</div>
            <button
                type="button"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                data-unit-add-toggle
                title="Tambah satuan"
                aria-label="Tambah satuan"
                aria-expanded="{{ $errors->has('nama_satuan') ? 'true' : 'false' }}"
            >
                <x-icons.plus class="h-5 w-5" />
            </button>
        </div>
        <div class="bakery-card-body pt-2">
            <div
                data-unit-add-form
                class="{{ $errors->has('nama_satuan') ? '' : 'hidden' }} mb-4 border-b border-slate-100 pb-4"
            >
                <form method="POST" action="{{ route($unitStoreRoute) }}" class="flex flex-col gap-3 sm:flex-row sm:items-start">
                    @csrf
                    <div class="flex-1">
                        <input
                            type="text"
                            name="nama_satuan"
                            value="{{ old('nama_satuan') }}"
                            class="bakery-input w-full @error('nama_satuan') ring-2 ring-rose-300 @enderror"
                            placeholder="Contoh: kg, pcs, liter"
                            required
                            autofocus
                        />
                        @error('nama_satuan')
                            <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="bakery-btn-primary shrink-0 whitespace-nowrap">Simpan</button>
                </form>
            </div>
            <div class="bakery-table-wrap">
                <table class="bakery-table">
                    <thead>
                        <tr>
                            <th>Nama Satuan</th>
                            <th class="w-[90px] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($units as $unit)
                            <tr>
                                <td class="font-semibold text-slate-800">{{ $unit->nama }}</td>
                                <td>
                                    <div class="flex items-center justify-center">
                                        <form id="delete-satuan-{{ $unit->id }}" method="POST" action="{{ route($unitDestroyRoute, $unit->id) }}" class="inline">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                            data-delete-confirm
                                            data-delete-form="delete-satuan-{{ $unit->id }}"
                                            data-confirm-message="Hapus satuan ini?"
                                            onclick="handleConfirmDelete(this)"
                                            title="Hapus"
                                            aria-label="Hapus"
                                        >
                                            <x-icons.trash />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-12 text-center text-sm text-slate-500">
                                    Belum ada satuan terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($materials as $m)
        @php
            $jumlahStok = (float) $m->jumlah;
            $minStok = (float) $m->min;
            $habis = $jumlahStok <= 0;
            $aman = ! $habis && $jumlahStok > $minStok;
            $statusLabel = $habis
                ? 'Stok Habis'
                : ($aman ? 'Stok Aman' : 'Perlu Diisi');
            $statusBadgeClass = $habis
                ? 'bg-rose-50 text-rose-600'
                : ($aman ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-700');
            $satuan = $m->satuan ?? 'kg';
            $totalNilai = (int) round((float) $m->jumlah * (int) $m->harga);
        @endphp
        <x-modal id="detail-stok-{{ $m->id }}" size="md" title="Detail Stok Bahan Baku" :subtitle="$m->id">
            <dl class="text-sm">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">Nama</dt>
                    <dd class="max-w-[60%] text-right font-semibold text-slate-800">{{ $m->nama }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">Jumlah</dt>
                    <dd class="font-semibold text-slate-800">{{ FormatHelper::formatQtyOne($m->jumlah) }} {{ $satuan }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">Batas Aman</dt>
                    <dd class="font-semibold text-slate-800">{{ FormatHelper::formatQtyOne($m->min) }} {{ $satuan }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">Harga (rata-rata tertimbang)</dt>
                    <dd class="font-semibold text-slate-800">{{ FormatHelper::rupiah($m->harga) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">Total Nilai</dt>
                    <dd class="font-semibold text-amber-600">{{ FormatHelper::rupiah($totalNilai) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">Status</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-bold {{ $statusBadgeClass }}">
                            {{ $statusLabel }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-2.5">
                    <dt class="text-slate-400">Terakhir Update</dt>
                    <dd class="font-semibold text-slate-800">
                        {{ $m->updated_at ? FormatHelper::dateId($m->updated_at) : '—' }}
                    </dd>
                </div>
            </dl>
            <div class="mt-4 border-t border-slate-100 pt-4">
                <div class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">Riwayat Restock</div>
                @if ($m->restocks->isNotEmpty())
                    <div class="max-h-48 overflow-y-auto rounded-lg border border-slate-100">
                        <table class="w-full text-xs">
                            <thead class="sticky top-0 bg-slate-50 text-left text-slate-500">
                                <tr>
                                    <th class="px-3 py-2 font-bold">Tanggal Pembelian</th>
                                    <th class="px-3 py-2 font-bold">Jumlah</th>
                                    <th class="px-3 py-2 font-bold">Harga Satuan</th>
                                    <th class="px-3 py-2 font-bold">Total Pembelian</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($m->restocks as $restock)
                                    <tr>
                                        <td class="px-3 py-2 text-slate-700">{{ FormatHelper::dateId($restock->tanggal) }}</td>
                                        <td class="px-3 py-2 font-semibold text-slate-800">{{ FormatHelper::formatQtyOne($restock->jumlah) }} {{ $satuan }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ FormatHelper::rupiah($restock->harga) }}</td>
                                        <td class="px-3 py-2 font-semibold text-slate-800">{{ FormatHelper::rupiah($restock->total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-slate-500">Belum ada riwayat restock.</p>
                @endif
            </div>
            <div class="mt-4 flex justify-end border-t border-slate-100 pt-3">
                <button type="button" class="bakery-btn-ghost text-sm" data-modal-close>Tutup</button>
            </div>
        </x-modal>
    @endforeach

    @foreach ($materials as $m)
        @php
            $selectedSatuan = old('satuan', $m->satuan ?? '');
            $unitNames = $units->pluck('nama');
            $editMin = old('min') !== null ? old('min') : FormatHelper::formatQtyInput($m->min);
            $isEditTarget = old('_edit_id') === $m->id;
        @endphp
        <x-modal
            id="edit-stok-{{ $m->id }}"
            title="Ubah Bahan Baku"
            :subtitle="$m->nama"
            :scrollable="false"
            :auto-open="$isEditTarget && ($errors->has('nama') || $errors->has('satuan') || $errors->has('min'))"
        >
            <form method="POST" action="{{ route($updateRoute, $m->id) }}" class="space-y-4" data-modal-form data-stock-form>
                @csrf @method('PUT')
                <input type="hidden" name="_edit_id" value="{{ $m->id }}" />
                <x-form-field label="Nama Bahan" name="nama" :value="old('nama', $m->nama)" required autofocus />
                <p class="text-xs text-slate-500">Jumlah dan harga diperbarui melalui restock, bukan dari form ubah.</p>
                <div class="min-w-0">
                    <label for="field-satuan-edit-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                        Satuan
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </label>
                    <select
                        id="field-satuan-edit-{{ $m->id }}"
                        name="satuan"
                        required
                        class="bakery-input h-11 w-full {{ $errors->has('satuan') && $isEditTarget ? '!ring-2 !ring-rose-400' : '' }}"
                    >
                        <option value="" disabled @selected($selectedSatuan === '')>Pilih Satuan</option>
                        @if ($selectedSatuan && ! $unitNames->contains($selectedSatuan))
                            <option value="{{ $selectedSatuan }}" @selected(true)>{{ $selectedSatuan }}</option>
                        @endif
                        @foreach ($units as $unit)
                            <option value="{{ $unit->nama }}" @selected($selectedSatuan === $unit->nama)>{{ $unit->nama }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('satuan') && $isEditTarget)
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('satuan') }}</p>
                    @endif
                </div>
                <div class="bakery-field">
                    <label for="field-min-edit-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                        Batas Aman
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input
                            id="field-min-edit-{{ $m->id }}"
                            name="min"
                            type="text"
                            value="{{ $editMin }}"
                            inputmode="decimal"
                            autocomplete="off"
                            data-decimal-one
                            required
                            class="bakery-input h-11 flex-1 {{ $errors->has('min') && $isEditTarget ? '!ring-2 !ring-rose-400' : '' }}"
                        />
                        <span
                            data-stock-unit-suffix
                            class="inline-flex h-11 min-w-[3rem] shrink-0 items-center justify-center rounded-lg bg-slate-100 px-2.5 text-xs font-bold uppercase text-slate-600"
                        >{{ $selectedSatuan ?: '—' }}</span>
                    </div>
                    @if ($errors->has('min') && $isEditTarget)
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('min') }}</p>
                    @endif
                </div>
                <x-form-actions />
            </form>
        </x-modal>
    @endforeach

    @foreach ($materials as $m)
        @php
            $satuan = $m->satuan ?? 'kg';
            $isRestockTarget = old('_restock_id') === $m->id;
            $restockTanggal = old('restock_tanggal', now()->toDateString());
            $restockJumlah = old('restock_jumlah', '');
            $restockHarga = old('restock_harga', '');
            $restockCatatan = old('restock_catatan', '');
        @endphp
        <x-modal
            id="restock-stok-{{ $m->id }}"
            title="Restock Bahan Baku"
            :scrollable="false"
            :auto-open="$isRestockTarget && ($errors->has('restock_tanggal') || $errors->has('restock_jumlah') || $errors->has('restock_harga') || $errors->has('restock_catatan'))"
        >
            <form method="POST" action="{{ route($restockRoute, $m->id) }}" class="space-y-4" data-modal-form>
                @csrf
                <input type="hidden" name="_restock_id" value="{{ $m->id }}" />
                <div class="rounded-lg bg-slate-50 px-3 py-2.5 text-sm">
                    <div class="flex items-center justify-between gap-4">
                        <span class="min-w-0 truncate font-bold text-slate-800">{{ $m->nama }}</span>
                        <div class="flex shrink-0 items-center gap-2">
                            <span class="text-slate-500">Stok Saat Ini</span>
                            <span class="font-bold text-slate-800">{{ FormatHelper::formatQtyOne($m->jumlah) }} {{ $satuan }}</span>
                        </div>
                    </div>
                </div>
                <div class="bakery-field">
                    <label for="field-restock-tanggal-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                        Tanggal Pembelian
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="field-restock-tanggal-{{ $m->id }}"
                        name="restock_tanggal"
                        type="date"
                        value="{{ $restockTanggal }}"
                        required
                        class="bakery-input h-11 w-full {{ $errors->has('restock_tanggal') && $isRestockTarget ? '!ring-2 !ring-rose-400' : '' }}"
                    />
                    @if ($errors->has('restock_tanggal') && $isRestockTarget)
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('restock_tanggal') }}</p>
                    @endif
                </div>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div class="min-w-0 flex-1">
                        <label for="field-restock-jumlah-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                            Jumlah Restock
                            <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input
                                id="field-restock-jumlah-{{ $m->id }}"
                                name="restock_jumlah"
                                type="text"
                                value="{{ $restockJumlah }}"
                                inputmode="decimal"
                                autocomplete="off"
                                data-decimal-one
                                required
                                class="bakery-input h-11 flex-1 {{ $errors->has('restock_jumlah') && $isRestockTarget ? '!ring-2 !ring-rose-400' : '' }}"
                            />
                            <span class="inline-flex h-11 min-w-[3rem] shrink-0 items-center justify-center rounded-lg bg-slate-100 px-2.5 text-xs font-bold uppercase text-slate-600">{{ $satuan }}</span>
                        </div>
                        @if ($errors->has('restock_jumlah') && $isRestockTarget)
                            <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('restock_jumlah') }}</p>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <label for="field-restock-harga-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                            Harga per {{ $satuan }}
                            <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="field-restock-harga-{{ $m->id }}"
                            name="restock_harga"
                            type="number"
                            value="{{ $restockHarga }}"
                            min="1"
                            required
                            class="bakery-input h-11 w-full {{ $errors->has('restock_harga') && $isRestockTarget ? '!ring-2 !ring-rose-400' : '' }}"
                        />
                        @if ($errors->has('restock_harga') && $isRestockTarget)
                            <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('restock_harga') }}</p>
                        @endif
                    </div>
                </div>
                <div class="bakery-field">
                    <label for="field-restock-catatan-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                        Catatan
                    </label>
                    <textarea
                        id="field-restock-catatan-{{ $m->id }}"
                        name="restock_catatan"
                        rows="2"
                        placeholder="Opsional — nomor faktur, supplier, dll."
                        class="bakery-input w-full {{ $errors->has('restock_catatan') && $isRestockTarget ? '!ring-2 !ring-rose-400' : '' }}"
                    >{{ $restockCatatan }}</textarea>
                    @if ($errors->has('restock_catatan') && $isRestockTarget)
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('restock_catatan') }}</p>
                    @endif
                </div>
                <x-form-actions />
            </form>
        </x-modal>
    @endforeach

    @php
        $createSatuan = old('satuan', '');
    @endphp
    <x-modal
        id="stok-baru"
        title="Tambah Bahan Baku"
        subtitle="Catat bahan baku baru untuk produksi"
        :scrollable="false"
        :auto-open="! old('_edit_id') && ! old('_restock_id') && ($errors->has('nama') || $errors->has('jumlah') || $errors->has('satuan') || $errors->has('min') || $errors->has('harga'))"
    >
        <form method="POST" action="{{ route($storeRoute) }}" class="space-y-4" data-modal-form data-stock-form>
            @csrf
            <x-form-field label="Nama Bahan" name="nama" :value="old('nama')" required autofocus />
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="min-w-0 flex-1">
                    <label for="field-jumlah-create" class="mb-1.5 block text-xs font-bold text-slate-600">
                        Jumlah
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="field-jumlah-create"
                        name="jumlah"
                        type="text"
                        value="{{ old('jumlah') }}"
                        inputmode="decimal"
                        autocomplete="off"
                        data-decimal-one
                        required
                        class="bakery-input h-11 w-full {{ $errors->has('jumlah') ? '!ring-2 !ring-rose-400' : '' }}"
                    />
                    @error('jumlah')
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div class="min-w-0 flex-1">
                    <label for="field-satuan-create" class="mb-1.5 block text-xs font-bold text-slate-600">
                        Satuan
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </label>
                    <select
                        id="field-satuan-create"
                        name="satuan"
                        required
                        class="bakery-input h-11 w-full {{ $errors->has('satuan') ? '!ring-2 !ring-rose-400' : '' }}"
                    >
                        <option value="" disabled @selected($createSatuan === '')>Pilih Satuan</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->nama }}" @selected($createSatuan === $unit->nama)>{{ $unit->nama }}</option>
                        @endforeach
                    </select>
                    @error('satuan')
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="bakery-field">
                <label for="field-min-create" class="mb-1.5 block text-xs font-bold text-slate-600">
                    Batas Aman
                    <span class="text-rose-500" aria-hidden="true">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <input
                        id="field-min-create"
                        name="min"
                        type="text"
                        value="{{ old('min') }}"
                        inputmode="decimal"
                        autocomplete="off"
                        data-decimal-one
                        required
                        class="bakery-input h-11 flex-1 {{ $errors->has('min') ? '!ring-2 !ring-rose-400' : '' }}"
                    />
                    <span
                        data-stock-unit-suffix
                        class="inline-flex h-11 min-w-[3rem] shrink-0 items-center justify-center rounded-lg bg-slate-100 px-2.5 text-xs font-bold uppercase text-slate-600"
                    >{{ $createSatuan ?: '—' }}</span>
                </div>
                @error('min')
                    <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <div class="bakery-field">
                <label for="field-harga-create" class="mb-1.5 block text-xs font-bold text-slate-600">
                    Harga per
                    <span data-stock-unit-suffix>{{ $createSatuan ?: '—' }}</span>
                    <span class="text-rose-500" aria-hidden="true">*</span>
                </label>
                <input
                    id="field-harga-create"
                    name="harga"
                    type="number"
                    value="{{ old('harga') }}"
                    min="0"
                    required
                    class="bakery-input {{ $errors->has('harga') ? '!ring-2 !ring-rose-400' : '' }}"
                />
                @error('harga')
                    <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <x-form-actions />
        </form>
    </x-modal>
</div>
