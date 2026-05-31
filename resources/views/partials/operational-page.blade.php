@php
    use App\Support\FormatHelper;

    $monthValue = $month->format('Y-m');
    $periodLabel = $month->translatedFormat('F Y');

    $pageUrl = static function (array $overrides = []) use ($monthValue, $filter): string {
        $query = array_filter([
            'month' => $monthValue,
            'tab' => $overrides['tab'] ?? request('tab', 'transaksi'),
            'jenis' => $overrides['jenis'] ?? $filter ?? null,
        ], fn ($v) => $v !== null && $v !== '');

        if (array_key_exists('jenis', $overrides) && $overrides['jenis'] === null) {
            unset($query['jenis']);
        }

        return request()->url().'?'.http_build_query($query);
    };

    $jenisLabel = static fn (string $jenis): string => $jenis === 'Fixed'
        ? 'Tetap'
        : 'Variabel';

    $fixedCategories = $categories->get('Fixed', collect());
    $variableCategories = $categories->get('Variable', collect());

    $categoryOptions = static function ($selectedId = null) use ($fixedCategories, $variableCategories, $jenisLabel): void {
        foreach ([['Fixed', $fixedCategories], ['Variable', $variableCategories]] as [$jenis, $list]) {
            if ($list->isEmpty()) {
                continue;
            }
            echo '<optgroup label="'.e($jenisLabel($jenis)).'">';
            foreach ($list as $cat) {
                $sel = (string) old('expense_category_id', $selectedId) === (string) $cat->id ? ' selected' : '';
                echo '<option value="'.e($cat->id).'"'.$sel.'>'.e($cat->nama).'</option>';
            }
            echo '</optgroup>';
        }
    };
@endphp
<div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <form method="GET" action="{{ request()->url() }}" class="flex flex-wrap items-end gap-3">
            @if ($tab === 'rekap')
                <input type="hidden" name="tab" value="rekap">
            @endif
            @if (! empty($filter))
                <input type="hidden" name="jenis" value="{{ $filter }}">
            @endif
            <div class="bakery-field min-w-[180px]">
                <label for="filter-month" class="mb-1.5 block text-xs font-bold text-slate-600">Periode</label>
                <input
                    id="filter-month"
                    type="month"
                    name="month"
                    value="{{ $monthValue }}"
                    class="bakery-input"
                    onchange="this.form.submit()"
                />
            </div>
        </form>

        <div class="inline-flex rounded-xl bg-slate-100 p-1">
            <a
                href="{{ $pageUrl(['tab' => 'transaksi']) }}"
                class="rounded-lg px-4 py-2 text-sm font-bold transition {{ $tab === 'transaksi' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}"
            >
                Transaksi
            </a>
            <a
                href="{{ $pageUrl(['tab' => 'rekap']) }}"
                class="rounded-lg px-4 py-2 text-sm font-bold transition {{ $tab === 'rekap' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}"
            >
                Rekap Bulan
            </a>
        </div>
    </div>

    <p class="mt-2 text-sm font-semibold text-slate-500">Menampilkan data {{ $periodLabel }}</p>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($stats as $s)
            @php $toneMap = ['violet' => 'violet', 'blue' => 'blue', 'amber' => 'amber']; @endphp
            <x-kpi-card :title="$s['label']" :value="$s['value']" :tone="$toneMap[$s['tone']] ?? 'amber'" :icon="$s['icon'] ?? null" />
        @endforeach
    </div>

    @if ($tab === 'rekap' && $summary)
        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <div class="bakery-card">
                <div class="bakery-card-header border-b border-slate-100 pb-3">
                    <div class="text-base font-extrabold text-sky-700">Biaya Tetap (Fixed Cost)</div>
                </div>
                <div class="bakery-card-body pt-2">
                    @if (count($summary['fixed']['rows']) > 0)
                        <ul class="divide-y divide-slate-100">
                            @foreach ($summary['fixed']['rows'] as $row)
                                <li class="flex items-center justify-between gap-4 py-3 text-sm">
                                    <span class="font-semibold text-slate-700">{{ $row['label'] }}</span>
                                    <span class="shrink-0 font-extrabold text-rose-600">{{ FormatHelper::rupiah($row['amount']) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="py-6 text-center text-sm text-slate-500">Tidak ada pengeluaran pada periode ini.</p>
                    @endif
                    <div class="mt-2 flex items-center justify-between border-t border-slate-200 pt-3">
                        <span class="text-sm font-extrabold text-slate-800">TOTAL</span>
                        <span class="text-base font-extrabold text-sky-700">{{ FormatHelper::rupiah($summary['fixed']['total']) }}</span>
                    </div>
                </div>
            </div>

            <div class="bakery-card">
                <div class="bakery-card-header border-b border-slate-100 pb-3">
                    <div class="text-base font-extrabold text-amber-700">Biaya Variabel (Variable Cost)</div>
                </div>
                <div class="bakery-card-body pt-2">
                    @if (count($summary['variable']['rows']) > 0)
                        <ul class="divide-y divide-slate-100">
                            @foreach ($summary['variable']['rows'] as $row)
                                <li class="flex items-center justify-between gap-4 py-3 text-sm">
                                    <span class="font-semibold text-slate-700">
                                        {{ $row['label'] }}
                                        @if (! empty($row['from_restock']))
                                            <span class="ml-1 inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">dari stok</span>
                                        @endif
                                    </span>
                                    <span class="shrink-0 font-extrabold text-rose-600">{{ FormatHelper::rupiah($row['amount']) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="py-6 text-center text-sm text-slate-500">Tidak ada pengeluaran pada periode ini.</p>
                    @endif
                    <div class="mt-2 flex items-center justify-between border-t border-slate-200 pt-3">
                        <span class="text-sm font-extrabold text-slate-800">TOTAL</span>
                        <span class="text-base font-extrabold text-amber-700">{{ FormatHelper::rupiah($summary['variable']['total']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 bakery-card">
            <div class="bakery-card-body flex flex-wrap items-center justify-between gap-3 py-4">
                <span class="text-sm font-extrabold text-slate-800">Total Pengeluaran Bulan Ini</span>
                <span class="text-xl font-extrabold text-violet-700">{{ FormatHelper::rupiah($summary['grand_total']) }}</span>
            </div>
        </div>

        <p class="mt-3 text-xs font-semibold text-slate-400">Belanja bahan baku dihitung otomatis dari menu Restock Stok. Input bahan produksi di sana, bukan di sini.</p>
    @else
        <div class="mt-6 bakery-card" data-table-search>
            <div class="bakery-card-header flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div class="text-lg font-extrabold text-slate-900">Daftar Biaya Operasional</div>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative inline-flex items-center" data-dropdown>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:border-amber-200 hover:text-amber-700 {{ ! empty($filter) ? 'border-amber-200 bg-amber-50 text-amber-800' : '' }}"
                            data-dropdown-button
                        >
                            <x-icons.filter class="h-3.5 w-3.5" />
                            {{ ! empty($filter) ? $jenisLabel($filter) : 'Semua jenis' }}
                        </button>
                        <div
                            class="absolute right-0 top-full z-50 mt-2 hidden min-w-[148px] rounded-xl bg-white p-1.5 shadow-lg ring-1 ring-black/10"
                            data-dropdown-menu
                        >
                            <a href="{{ $pageUrl(['jenis' => null]) }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 {{ empty($filter) ? 'bg-amber-50 text-amber-800' : '' }}">Semua</a>
                            <a href="{{ $pageUrl(['jenis' => 'Fixed']) }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 {{ ($filter ?? '') === 'Fixed' ? 'bg-amber-50 text-amber-800' : '' }}">Tetap</a>
                            <a href="{{ $pageUrl(['jenis' => 'Variable']) }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 {{ ($filter ?? '') === 'Variable' ? 'bg-amber-50 text-amber-800' : '' }}">Variabel</a>
                        </div>
                    </div>
                    <x-table-search placeholder="Cari biaya..." :value="''" />
                </div>
            </div>

            <div class="bakery-card-body bakery-table-wrap pt-2">
                <table class="bakery-table">
                    <thead>
                        <tr>
                            <th class="w-[120px]">Tanggal</th>
                            <th class="w-[160px]">Kategori</th>
                            <th class="w-[100px]">Deskripsi</th>
                            <th class="w-[140px]">Jumlah</th>
                            <th class="w-[110px]">Jenis</th>
                            @if ($canEdit ?? true)
                                <th class="w-[90px] text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody data-table-search-body>
                        @forelse ($costs as $c)
                            <tr
                                data-searchable-row
                                data-search="{{ strtolower($c->kat.' '.($c->desk ?? '').' '.$c->jenis.' '.$jenisLabel($c->jenis)) }}"
                            >
                                <td>{{ FormatHelper::dateId($c->tanggal) }}</td>
                                <td class="font-semibold text-slate-800">{{ $c->kat }}</td>
                                <td class="max-w-[100px]">
                                    <div class="flex items-center gap-1">
                                        <span class="min-w-0 flex-1 truncate text-xs text-slate-500" title="{{ $c->desk }}">{{ $c->desk ?: '—' }}</span>
                                        <button
                                            type="button"
                                            class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-md text-slate-400 transition hover:bg-slate-100 hover:text-sky-600"
                                            data-modal-open="detail-cost-{{ $c->id }}"
                                            title="Lihat detail"
                                            aria-label="Lihat detail"
                                        >
                                            <x-icons.info-circle class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </td>
                                <td class="font-extrabold text-rose-600">{{ FormatHelper::rupiah($c->jumlah) }}</td>
                                <td>
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $c->jenis === 'Fixed' ? 'bg-sky-50 text-sky-600' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $jenisLabel($c->jenis) }}
                                    </span>
                                </td>
                                @if ($canEdit ?? true)
                                    <td>
                                        <div class="flex items-center justify-center gap-1">
                                            <button
                                                type="button"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                                                data-modal-open="edit-cost-{{ $c->id }}"
                                                title="Edit"
                                                aria-label="Edit"
                                            >
                                                <x-icons.pencil />
                                            </button>
                                            <form id="delete-cost-{{ $c->id }}" method="POST" action="{{ route($destroyRoute, $c->id) }}" class="inline">
                                                @csrf @method('DELETE')
                                            </form>
                                            <button
                                                type="button"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                                onclick="if (window.confirm('Hapus biaya operasional ini?')) document.getElementById('delete-cost-{{ $c->id }}').submit()"
                                                title="Hapus"
                                                aria-label="Hapus"
                                            >
                                                <x-icons.trash />
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr data-table-empty>
                                <td colspan="{{ ($canEdit ?? true) ? 6 : 5 }}" class="px-4 py-12 text-center text-sm text-slate-500">
                                    Data tidak ditemukan
                                </td>
                            </tr>
                        @endforelse
                        <tr data-table-no-results class="hidden">
                            <td colspan="{{ ($canEdit ?? true) ? 6 : 5 }}" class="px-4 py-12 text-center text-sm text-slate-500">
                                Data tidak ditemukan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if ($canManageCategories ?? false)
            <div class="bakery-card mt-6" data-category-card>
                <div class="bakery-card-header flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <div class="text-lg font-extrabold text-slate-900">Daftar Kategori Biaya</div>
                        <p class="mt-0.5 text-xs font-semibold text-slate-400">Kelola kategori untuk dropdown input biaya operasional</p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                        data-category-add-toggle
                        title="Tambah Kategori"
                        aria-label="Tambah Kategori"
                        aria-expanded="{{ $errors->has('nama') && ! $errors->has('tanggal') ? 'true' : 'false' }}"
                    >
                        <x-icons.plus class="h-5 w-5" />
                    </button>
                </div>
                <div class="bakery-card-body pt-2">
                    <div
                        data-category-add-form
                        class="{{ $errors->has('nama') && ! $errors->has('tanggal') ? '' : 'hidden' }} mb-4 border-b border-slate-100 pb-4"
                    >
                        <form method="POST" action="{{ route($categoryStoreRoute) }}" class="grid gap-3 sm:grid-cols-3">
                            @csrf
                            <div>
                                <input
                                    type="text"
                                    name="nama"
                                    value="{{ old('nama') }}"
                                    class="bakery-input w-full @error('nama') ring-2 ring-rose-300 @enderror"
                                    placeholder="Contoh: Sewa Toko"
                                    required
                                />
                                @error('nama')
                                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <select name="jenis" class="bakery-input w-full @error('jenis') ring-2 ring-rose-300 @enderror" required>
                                    <option value="">Pilih jenis</option>
                                    <option value="Fixed" @selected(old('jenis') === 'Fixed')>Tetap</option>
                                    <option value="Variable" @selected(old('jenis') === 'Variable')>Variabel</option>
                                </select>
                                @error('jenis')
                                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-start">
                                <button type="submit" class="bakery-btn-primary w-full whitespace-nowrap sm:w-auto">Simpan</button>
                            </div>
                        </form>
                    </div>

                    <div class="bakery-table-wrap">
                        <table class="bakery-table">
                            <thead>
                                <tr>
                                    <th>Nama Kategori</th>
                                    <th class="w-[110px]">Jenis</th>
                                    <th class="w-[100px]">Status</th>
                                    <th class="w-[90px] text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allCategories as $cat)
                                    <tr class="{{ ! $cat->is_active ? 'opacity-60' : '' }}">
                                        <td class="font-semibold text-slate-800">{{ $cat->nama }}</td>
                                        <td>
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $cat->jenis === 'Fixed' ? 'bg-sky-50 text-sky-600' : 'bg-amber-50 text-amber-700' }}">
                                                {{ $jenisLabel($cat->jenis) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($cat->is_active)
                                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Aktif</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex items-center justify-center gap-1">
                                                <button
                                                    type="button"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                                                    data-modal-open="edit-kategori-{{ $cat->id }}"
                                                    title="Edit"
                                                    aria-label="Edit"
                                                >
                                                    <x-icons.pencil />
                                                </button>
                                                @if ($cat->canBeDeleted())
                                                    <form id="delete-kategori-{{ $cat->id }}" method="POST" action="{{ route($categoryDestroyRoute, $cat->id) }}" class="inline">
                                                        @csrf @method('DELETE')
                                                    </form>
                                                    <button
                                                        type="button"
                                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                                        data-delete-confirm
                                                        data-delete-form="delete-kategori-{{ $cat->id }}"
                                                        data-confirm-message="Hapus kategori biaya ini?"
                                                        onclick="handleConfirmDelete(this)"
                                                        title="Hapus"
                                                        aria-label="Hapus"
                                                    >
                                                        <x-icons.trash />
                                                    </button>
                                                @else
                                                    <span
                                                        class="inline-flex h-8 w-8 items-center justify-center text-slate-300"
                                                        title="Masih dipakai pada transaksi biaya"
                                                        aria-hidden="true"
                                                    >
                                                        <x-icons.trash />
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center text-sm text-slate-500">
                                            Belum ada kategori biaya.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @foreach ($allCategories as $cat)
                <x-modal id="edit-kategori-{{ $cat->id }}" title="Edit Kategori Biaya" :subtitle="$cat->nama">
                    <form method="POST" action="{{ route($categoryUpdateRoute, $cat->id) }}" class="space-y-4" data-modal-form>
                        @csrf @method('PUT')
                        <x-form-field label="Nama Kategori" name="nama" :value="old('nama', $cat->nama)" required />
                        <x-form-field label="Jenis" name="jenis" type="select" required>
                            <option value="Fixed" @selected(old('jenis', $cat->jenis) === 'Fixed')>Tetap</option>
                            <option value="Variable" @selected(old('jenis', $cat->jenis) === 'Variable')>Variabel</option>
                        </x-form-field>
                        <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                class="h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500"
                                @checked(old('is_active', $cat->is_active))
                            />
                            <span class="text-sm font-semibold text-slate-700">Kategori aktif (tampil di dropdown)</span>
                        </label>
                        @if ($cat->operational_costs_count > 0)
                            <p class="text-xs font-semibold text-slate-400">Digunakan di {{ $cat->operational_costs_count }} transaksi. Nonaktifkan jika tidak dipakai lagi.</p>
                        @endif
                        <x-form-actions />
                    </form>
                </x-modal>
            @endforeach
        @endif

        @foreach ($costs as $c)
            <x-modal id="detail-cost-{{ $c->id }}" size="sm" title="Detail Biaya Operasional" :subtitle="$c->id">
                <dl class="text-sm">
                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                        <dt class="text-slate-400">Tanggal</dt>
                        <dd class="font-semibold text-slate-800">{{ FormatHelper::dateId($c->tanggal) }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                        <dt class="text-slate-400">Kategori</dt>
                        <dd class="max-w-[60%] text-right font-semibold text-slate-800">{{ $c->kat }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                        <dt class="text-slate-400">Jenis</dt>
                        <dd>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $c->jenis === 'Fixed' ? 'bg-sky-50 text-sky-600' : 'bg-amber-50 text-amber-700' }}">
                                {{ $jenisLabel($c->jenis) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                        <dt class="text-slate-400">Jumlah</dt>
                        <dd class="font-extrabold text-rose-600">{{ FormatHelper::rupiah($c->jumlah) }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4 py-2.5">
                        <dt class="shrink-0 text-slate-400">Deskripsi</dt>
                        <dd class="max-w-[65%] text-right text-sm font-semibold text-slate-800">{{ $c->desk ?: '—' }}</dd>
                    </div>
                    @if ($c->journalTransaction?->ref)
                        <div class="flex items-center justify-between gap-4 border-t border-slate-100 py-2.5">
                            <dt class="text-slate-400">Ref. Jurnal</dt>
                            <dd class="font-semibold text-slate-800">{{ $c->journalTransaction->ref }}</dd>
                        </div>
                    @endif
                </dl>
                <div class="mt-4 flex justify-end border-t border-slate-100 pt-3">
                    <button type="button" class="bakery-btn-ghost text-sm" data-modal-close>Tutup</button>
                </div>
            </x-modal>
        @endforeach

        @if ($canEdit ?? true)
            @php
                $editCostId = old('_edit_id');
                $hasCostErrors = $errors->has('tanggal') || $errors->has('expense_category_id') || $errors->has('jumlah') || $errors->has('desk');
            @endphp
            @foreach ($costs as $c)
                <x-modal id="edit-cost-{{ $c->id }}" title="Edit Biaya Operasional" :subtitle="$c->kat" :auto-open="$editCostId === $c->id && $hasCostErrors">
                    <form method="POST" action="{{ route($updateRoute, $c->id) }}" class="space-y-4" data-modal-form>
                        @csrf @method('PUT')
                        <input type="hidden" name="_edit_id" value="{{ $c->id }}" />
                        <x-form-field label="Tanggal" name="tanggal" type="date" :value="old('tanggal', $c->tanggal->format('Y-m-d'))" required autofocus />
                        <x-form-field label="Kategori" name="expense_category_id" type="select" required>
                            @php $categoryOptions($c->expense_category_id); @endphp
                        </x-form-field>
                        <x-form-field label="Nominal (Rp)" name="jumlah" type="number" :value="old('jumlah', $c->jumlah)" min="1" required />
                        <x-form-field
                            label="Deskripsi"
                            name="desk"
                            type="textarea"
                            :value="old('desk', $c->desk)"
                            placeholder="Opsional — no meter, nama karyawan, supplier, dll."
                            helper="Kosongkan jika kategori sudah cukup jelas."
                        />
                        <x-form-actions />
                    </form>
                </x-modal>
            @endforeach
        @endif

        @if ($canAdd ?? true)
            <x-modal
                id="cost-baru"
                title="Tambah Biaya Operasional"
                subtitle="Catat pengeluaran tetap dan variabel per bulan"
                :auto-open="! old('_edit_id') && ($errors->has('tanggal') || $errors->has('expense_category_id') || $errors->has('jumlah'))"
            >
                <form method="POST" action="{{ route($storeRoute) }}" class="space-y-4" data-modal-form>
                    @csrf
                    <x-form-field label="Tanggal" name="tanggal" type="date" :value="old('tanggal', date('Y-m-d'))" required autofocus />
                    <x-form-field label="Kategori" name="expense_category_id" type="select" required>
                        <option value="">— Pilih kategori —</option>
                        @php $categoryOptions(); @endphp
                    </x-form-field>
                    <x-form-field label="Nominal (Rp)" name="jumlah" type="number" :value="old('jumlah')" min="1" required />
                    <x-form-field
                        label="Deskripsi"
                        name="desk"
                        type="textarea"
                        :value="old('desk')"
                        placeholder="Opsional — no meter, nama karyawan, supplier, dll."
                        helper="Kosongkan jika kategori sudah cukup jelas."
                    />
                    <x-form-actions />
                </form>
            </x-modal>
        @endif
    @endif
</div>
