@php
    use App\Models\RawMaterial;
    use App\Support\FormatHelper;

    $indexRoute = $indexRoute ?? request()->route()->getName();
    $showRoute = $showRoute ?? 'admin.stok.show';
    $kategoriFilter = $filter ?? request('kategori');
    $pageUrl = static function (array $overrides = []) use ($indexRoute, $kategoriFilter): string {
        $query = array_filter([
            'search' => request('search'),
            'kategori' => $overrides['kategori'] ?? $kategoriFilter ?? null,
        ], static fn ($v) => $v !== null && $v !== '');

        if (array_key_exists('kategori', $overrides) && $overrides['kategori'] === null) {
            unset($query['kategori']);
        }

        return route($indexRoute, $query);
    };
    $kategoriBadgeClass = static fn (?string $kategori): string => match ($kategori) {
        RawMaterial::KATEGORI_KERING => 'bg-amber-50 text-amber-700',
        RawMaterial::KATEGORI_BASAH => 'bg-sky-50 text-sky-600',
        default => 'bg-violet-50 text-violet-700',
    };
@endphp
<div>
    <div class="bakery-card" data-table-search>
        <div class="bakery-card-header flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div class="text-lg font-extrabold text-slate-900">Daftar Stok Bahan Baku</div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative inline-flex items-center" data-dropdown>
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:border-amber-200 hover:text-amber-700 {{ ! empty($kategoriFilter) ? 'border-amber-200 bg-amber-50 text-amber-800' : '' }}"
                        data-dropdown-button
                    >
                        <x-icons.filter class="h-3.5 w-3.5" />
                        {{ ! empty($kategoriFilter) ? (RawMaterial::kategoriOptions()[$kategoriFilter] ?? 'Semua kategori') : 'Semua kategori' }}
                    </button>
                    <div
                        class="absolute right-0 top-full z-50 mt-2 hidden min-w-[148px] rounded-xl bg-white p-1.5 shadow-lg ring-1 ring-black/10"
                        data-dropdown-menu
                    >
                        <a href="{{ $pageUrl(['kategori' => null]) }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 {{ empty($kategoriFilter) ? 'bg-amber-50 text-amber-800' : '' }}">Semua</a>
                        @foreach (RawMaterial::kategoriOptions() as $value => $label)
                            <a href="{{ $pageUrl(['kategori' => $value]) }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 {{ ($kategoriFilter ?? '') === $value ? 'bg-amber-50 text-amber-800' : '' }}">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>
                <x-table-search
                    placeholder="Cari bahan baku..."
                    :value="$search ?? ''"
                />
            </div>
        </div>
        <div class="bakery-card-body bakery-table-wrap pt-2">
            <table class="bakery-table">
                <thead>
                    <tr>
                        <th class="w-[90px]">ID</th>
                        <th>Nama</th>
                        <th class="w-[100px]">Kategori</th>
                        <th class="w-[120px]">Jumlah</th>
                        <th class="w-[160px] text-center">Status</th>
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
                            $kategori = $m->kategori ?? RawMaterial::KATEGORI_PADAT;
                        @endphp
                        <tr
                            data-searchable-row
                            data-search="{{ strtolower($m->id.' '.$m->nama.' '.$kategori.' '.$m->kategoriLabel().' '.$satuan.' '.$statusLabel) }}"
                        >
                            <td class="font-bold text-slate-800">{{ $m->id }}</td>
                            <td class="max-w-md truncate">
                                <a href="{{ route($showRoute, $m->id) }}" class="font-semibold text-slate-800 transition hover:text-sky-700">
                                    {{ $m->nama }}
                                </a>
                            </td>
                            <td>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $kategoriBadgeClass($kategori) }}">
                                    {{ $m->kategoriLabel() }}
                                </span>
                            </td>
                            <td>
                                <x-unit-qty :qty="$m->jumlah" :unit="$satuan" />
                            </td>
                            <td class="text-center">
                                <a
                                    href="{{ route($showRoute, $m->id) }}"
                                    class="inline-flex cursor-pointer items-center rounded-full px-2.5 py-1.5 text-xs font-bold no-underline transition hover:opacity-90 {{ $statusClass }}"
                                    title="Lihat detail"
                                    aria-label="Lihat detail: {{ $statusLabel }}"
                                >
                                    {{ $statusLabel }}
                                </a>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a
                                        href="{{ route($showRoute, $m->id) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-sky-50 hover:text-sky-600"
                                        title="Detail"
                                        aria-label="Detail"
                                    >
                                        <x-icons.info-circle class="h-4 w-4" />
                                    </a>
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
                                    @if ($m->canBeDeleted())
                                        <form id="delete-stok-{{ $m->id }}" method="POST" action="{{ route($destroyRoute, $m->id) }}" class="inline">
                                            @csrf @method('DELETE')
                                        </form>
                                    @endif
                                    <button
                                        type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                        data-delete-blocked="{{ $m->canBeDeleted() ? '0' : '1' }}"
                                        data-blocked-message="Masih dipakai pada data produksi."
                                        @if ($m->canBeDeleted())
                                            data-delete-form="delete-stok-{{ $m->id }}"
                                        @endif
                                        data-confirm-message="Hapus bahan baku ini?"
                                        onclick="handleBlockedDelete(this)"
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
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">
                                @if (! empty($kategoriFilter) || filled($search ?? null))
                                    Data tidak ditemukan
                                @else
                                    Tidak ada bahan baku yang perlu diisi ulang
                                @endif
                            </td>
                        </tr>
                    @endforelse
                    <tr data-table-no-results class="hidden">
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">
                            Data tidak ditemukan
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
                            placeholder="Contoh: kg, pcs, L"
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
                            @php
                                $unitDeleteBlocked = ! $unit->canBeDeleted();
                                $unitBlockedMessage = $unit->isProtected()
                                    ? 'Satuan sistem tidak dapat dihapus.'
                                    : 'Masih dipakai bahan baku.';
                            @endphp
                            <tr>
                                <td class="font-semibold text-slate-800">
                                    {{ $unit->nama }}
                                    @if ($unit->isProtected())
                                        <span class="ml-1.5 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">Sistem</span>
                                    @elseif ($unit->isInUse())
                                        <span class="ml-1.5 rounded bg-amber-50 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700">Dipakai</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-center">
                                        @if ($unit->canBeDeleted())
                                            <form id="delete-satuan-{{ $unit->id }}" method="POST" action="{{ route($unitDestroyRoute, $unit->id) }}" class="inline">
                                                @csrf @method('DELETE')
                                            </form>
                                        @endif
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                            data-delete-blocked="{{ $unitDeleteBlocked ? '1' : '0' }}"
                                            data-blocked-message="{{ $unitBlockedMessage }}"
                                            @if ($unit->canBeDeleted())
                                                data-delete-form="delete-satuan-{{ $unit->id }}"
                                            @endif
                                            data-confirm-message="Hapus satuan ini?"
                                            onclick="handleBlockedDelete(this)"
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
        @include('partials.stock-material-action-modals', ['m' => $m])
    @endforeach

    @php
        $createSatuan = old('satuan', '');
    @endphp
    <x-modal
        id="stok-baru"
        title="Tambah Bahan Baku"
        size="lg"
        :scrollable="false"
        :auto-open="! old('_edit_id') && ! old('_restock_id') && ($errors->has('nama') || $errors->has('kategori') || $errors->has('jumlah') || $errors->has('satuan') || $errors->has('min') || $errors->has('harga') || $errors->has('kode_produksi') || $errors->has('expired'))"
    >
        <form method="POST" action="{{ route($storeRoute) }}" class="space-y-3" data-modal-form data-stock-form>
            @csrf
            <div class="grid gap-3 sm:grid-cols-2 [&_.bakery-field+.bakery-field]:!mt-0">
                <x-form-field label="Nama Bahan" name="nama" :value="old('nama')" required autofocus />
                <x-form-field label="Kategori Bahan" name="kategori" type="select" :value="old('kategori')" required>
                    <option value="" disabled @selected(old('kategori') === null || old('kategori') === '')>Pilih kategori</option>
                    @foreach (RawMaterial::kategoriOptions() as $value => $label)
                        <option value="{{ $value }}" @selected(old('kategori') === $value)>{{ $label }}</option>
                    @endforeach
                </x-form-field>
            </div>
            <div class="grid gap-3 sm:grid-cols-2 [&_.bakery-field+.bakery-field]:!mt-0">
                <div class="min-w-0">
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
                <div class="min-w-0">
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
            <div class="grid gap-3 sm:grid-cols-2 [&_.bakery-field+.bakery-field]:!mt-0">
                <div class="bakery-field !mt-0">
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
                        >{{ $createSatuan ?: 'â€”' }}</span>
                    </div>
                    @error('min')
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div class="bakery-field !mt-0">
                    <label for="field-harga-create" class="mb-1.5 block text-xs font-bold text-slate-600">
                        Harga per
                        <span data-stock-unit-suffix>{{ $createSatuan ?: 'â€”' }}</span>
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="field-harga-create"
                        name="harga"
                        type="number"
                        value="{{ old('harga') }}"
                        min="0"
                        required
                        class="bakery-input h-11 w-full {{ $errors->has('harga') ? '!ring-2 !ring-rose-400' : '' }}"
                    />
                    @error('harga')
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="rounded-xl bg-slate-50/80 p-3 ring-1 ring-slate-100">
                <p class="mb-2 text-[11px] font-bold uppercase tracking-wide text-slate-500">Info Batch Stok Awal</p>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="min-w-0">
                        <label for="field-kode-create" class="mb-1.5 block text-xs font-bold text-slate-600">
                            Kode Produksi
                            <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="field-kode-create"
                            name="kode_produksi"
                            type="text"
                            value="{{ old('kode_produksi') }}"
                            placeholder="Kode produksi"
                            class="bakery-input h-11 w-full {{ $errors->has('kode_produksi') ? '!ring-2 !ring-rose-400' : '' }}"
                        />
                        @error('kode_produksi')
                            <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="min-w-0">
                        <label for="field-expired-create" class="mb-1.5 block text-xs font-bold text-slate-600">
                            Tanggal Expired
                            <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="field-expired-create"
                            name="expired"
                            type="date"
                            value="{{ old('expired') }}"
                            class="bakery-input h-11 w-full {{ $errors->has('expired') ? '!ring-2 !ring-rose-400' : '' }}"
                        />
                        @error('expired')
                            <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <x-form-actions compact />
        </form>
    </x-modal>
</div>
