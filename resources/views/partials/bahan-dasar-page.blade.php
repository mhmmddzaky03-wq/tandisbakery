@php
    use App\Support\FormatHelper;

    $indexRoute = $indexRoute ?? 'admin.bahan_dasar';
    $showRoute = $showRoute ?? 'admin.bahan_dasar.show';
    $storeRoute = $storeRoute ?? 'admin.bahan_dasar.store';
    $updateRoute = $updateRoute ?? 'admin.bahan_dasar.update';
    $destroyRoute = $destroyRoute ?? 'admin.bahan_dasar.destroy';
@endphp
<div>
    <div class="bakery-card" data-table-search>
        <div class="bakery-card-header bakery-card-header--bordered">
            <div class="bakery-card-header__title">Daftar Bahan Dasar</div>
            <div class="bakery-card-header__actions">
            <x-table-search placeholder="Cari bahan dasar..." :value="$search ?? ''" />
            </div>
        </div>
        <div class="bakery-card-body bakery-table-wrap pt-2">
            <table class="bakery-table">
                <colgroup>
                    <col class="w-[5.5rem]" />
                    <col />
                    <col class="w-[9rem]" />
                    <col class="w-[8rem]" />
                    <col class="w-[7.5rem]" />
                </colgroup>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th class="text-right">Stok</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody data-table-search-body>
                    @forelse ($items as $item)
                        @php
                            $jumlahStok = (float) $item->jumlah;
                            $minStok = (float) $item->min;
                            $habis = $jumlahStok <= 0;
                            $aman = ! $habis && $jumlahStok > $minStok;
                            $statusLabel = $habis ? 'Stok Habis' : ($aman ? 'Stok Aman' : 'Perlu Diisi');
                            $statusClass = $habis
                                ? 'bg-rose-50 text-rose-600 hover:bg-rose-100'
                                : ($aman ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-amber-50 text-amber-700 hover:bg-amber-100');
                            $satuan = $item->satuan ?? 'g';
                        @endphp
                        <tr data-searchable-row data-search="{{ strtolower($item->id.' '.$item->nama.' '.$satuan.' '.$statusLabel) }}">
                            <td class="whitespace-nowrap font-bold text-slate-800">
                                <a href="{{ route($showRoute, $item->id) }}" class="hover:text-sky-600">{{ $item->id }}</a>
                            </td>
                            <td class="min-w-[10rem]">
                                <a href="{{ route($showRoute, $item->id) }}" class="font-semibold text-slate-800 hover:text-sky-600">{{ $item->nama }}</a>
                                <div class="mt-0.5 text-[11px] font-semibold text-slate-400">{{ $item->batches_count }} batch adonan</div>
                            </td>
                            <td class="whitespace-nowrap text-right font-bold tabular-nums text-emerald-700">
                                {{ number_format($jumlahStok, 0, ',', '.') }} {{ $satuan }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route($showRoute, $item->id) }}" class="inline-flex rounded-full px-2.5 py-1.5 text-xs font-bold no-underline transition {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </a>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route($showRoute, $item->id) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-sky-50 hover:text-sky-600" title="Detail" aria-label="Detail">
                                        <x-icons.info-circle class="h-4 w-4" />
                                    </a>
                                    <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600" data-modal-open="edit-bahan-dasar-{{ $item->id }}" title="Edit" aria-label="Edit">
                                        <x-icons.pencil />
                                    </button>
                                    <form method="POST" action="{{ route($destroyRoute, $item->id) }}" class="inline" onsubmit="return confirm('Hapus bahan dasar ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus" aria-label="Hapus" @disabled(! $item->canBeDeleted())>
                                            <x-icons.trash />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr data-table-empty>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500">Belum ada bahan dasar. Tambahkan jenis adonan beserta bahan bakunya.</td>
                        </tr>
                    @endforelse
                    <tr data-table-no-results class="hidden">
                        <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500">Data tidak ditemukan</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @php
        $editId = old('_edit_id');
        $hasMasterErrors = $errors->has('nama') || $errors->has('satuan') || $errors->has('min');
        $hasCreateErrors = $errors->has('tanggal') || $errors->has('jumlah_hasil') || $errors->has('catatan') || $errors->has('materials') || $errors->has('materials.*');
        $isCreateTarget = ! $editId && ($hasMasterErrors || $hasCreateErrors);
    @endphp

    <x-modal id="bahan-dasar-baru" title="Tambah Bahan Dasar" subtitle="Buat jenis adonan dan batch pertama" size="lg" :scrollable="true" :auto-open="$isCreateTarget">
        <form id="form-bahan-dasar-baru" method="POST" action="{{ route($storeRoute) }}" data-modal-form data-production-form>
            @csrf
            @include('partials.bahan-dasar-form-body', [
                'prefix' => 'create',
                'isCreateTarget' => $isCreateTarget,
                'materials' => $materials ?? collect(),
                'materialRows' => old('materials', [['raw_material_id' => '', 'raw_material_restock_id' => '', 'jumlah' => '', 'satuan' => '']]),
            ])
        </form>
        <x-slot:footer>
            <x-form-actions form="form-bahan-dasar-baru" compact submit="Simpan Bahan Dasar" />
        </x-slot:footer>
    </x-modal>

    @foreach ($items as $item)
        @php $isEditTarget = $editId === $item->id && $hasMasterErrors; @endphp
        <x-modal id="edit-bahan-dasar-{{ $item->id }}" title="Edit Bahan Dasar" :subtitle="$item->id" size="md" :auto-open="$isEditTarget">
            <form method="POST" action="{{ route($updateRoute, $item->id) }}" class="space-y-3" data-modal-form>
                @csrf @method('PUT')
                <input type="hidden" name="_edit_id" value="{{ $item->id }}" />
                <div>
                    <label for="field-nama-bd-edit-{{ $item->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">Nama Bahan Dasar <span class="text-rose-500">*</span></label>
                    <input id="field-nama-bd-edit-{{ $item->id }}" name="nama" type="text" value="{{ old('nama', $item->nama) }}" required class="bakery-input h-11 w-full" />
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="field-satuan-bd-edit-{{ $item->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">Satuan <span class="text-rose-500">*</span></label>
                        <select id="field-satuan-bd-edit-{{ $item->id }}" name="satuan" required class="bakery-input h-11 w-full" @disabled($item->batches_count > 0)>
                            <option value="g" @selected(old('satuan', $item->satuan) === 'g')>Gram (g)</option>
                            <option value="kg" @selected(old('satuan', $item->satuan) === 'kg')>Kilogram (kg)</option>
                        </select>
                    </div>
                    <div>
                        <label for="field-min-bd-edit-{{ $item->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">Batas Aman <span class="text-rose-500">*</span></label>
                        <input id="field-min-bd-edit-{{ $item->id }}" name="min" type="text" value="{{ old('min', FormatHelper::formatQtyInput($item->min)) }}" required inputmode="decimal" data-decimal-one class="bakery-input h-11 w-full" />
                    </div>
                </div>
                <x-form-actions compact submit="Simpan Perubahan" />
            </form>
        </x-modal>
    @endforeach
</div>
