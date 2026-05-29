@php use App\Support\FormatHelper; @endphp
<div>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $s)
            @php $toneMap = ['blue' => 'blue', 'green' => 'green', 'rose' => 'rose', 'amber' => 'amber', 'slate' => 'amber']; @endphp
            <x-kpi-card :title="$s['label']" :value="$s['value']" :tone="$toneMap[$s['tone']] ?? 'amber'" :icon="$s['icon'] ?? null" />
        @endforeach
    </div>

    <div class="mt-5 bakery-card" data-table-search>
        <div class="bakery-card-header flex items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div class="min-w-0 shrink text-lg font-extrabold text-slate-900">Daftar Data Produksi</div>
            <x-table-search
                placeholder="Cari produksi..."
                :value="$search ?? ''"
            />
        </div>

        <div class="bakery-card-body bakery-table-wrap pt-4">
            <table class="bakery-table">
                <thead>
                    <tr>
                        <th class="w-[90px]">ID</th>
                        <th class="w-[120px]">Tanggal</th>
                        <th>Nama Produk</th>
                        <th class="w-[130px]">Jumlah</th>
                        <th class="w-[140px]">Status</th>
                        @if ($canEdit ?? true)
                            <th class="w-[90px] text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody data-table-search-body>
                    @forelse ($records as $r)
                        <tr
                            data-searchable-row
                            data-search="{{ strtolower($r->id.' '.$r->product_name.' '.$r->status.' '.($r->product?->id ?? '').' '.($r->notes ?? '')) }}"
                        >
                            <td class="font-bold text-slate-800">{{ $r->id }}</td>
                            <td>{{ FormatHelper::dateId($r->tanggal) }}</td>
                            <td>{{ $r->product_name }}</td>
                            <td>{{ number_format($r->jumlah, 0, ',', '.') }} {{ $r->satuan }}</td>
                            <td>
                                <button
                                    type="button"
                                    class="inline-flex w-[7rem] cursor-pointer items-center justify-between gap-2 rounded-full border-0 px-3 py-1.5 text-xs font-bold transition hover:opacity-90 {{ $r->status === 'Berhasil' ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }}"
                                    data-modal-open="detail-prod-{{ $r->id }}"
                                    title="Lihat detail"
                                    aria-label="Lihat detail: {{ $r->status }}"
                                >
                                    <span class="truncate">{{ $r->status }}</span>
                                    <x-icons.info-circle class="h-3.5 w-3.5 shrink-0 opacity-80" />
                                </button>
                            </td>
                            @if ($canEdit ?? true)
                                <td>
                                    <div class="flex items-center justify-center gap-1">
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                                            data-modal-open="edit-prod-{{ $r->id }}"
                                            title="Edit"
                                            aria-label="Edit"
                                        >
                                            <x-icons.pencil />
                                        </button>
                                        <form id="delete-prod-{{ $r->id }}" method="POST" action="{{ route($destroyRoute, $r->id) }}" class="inline">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                            data-production-delete
                                            data-has-product="{{ $r->product ? '1' : '0' }}"
                                            data-delete-form="delete-prod-{{ $r->id }}"
                                            data-linked-message="Data tidak dapat dihapus. Sudah terdaftar sebagai produk."
                                            data-confirm-message="Hapus data produksi ini?"
                                            onclick="handleProductionDelete(this)"
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
                                Belum ada data produksi. Catat produksi pertama untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                    <tr data-table-no-results class="hidden">
                        <td colspan="{{ ($canEdit ?? true) ? 6 : 5 }}" class="px-4 py-12 text-center text-sm text-slate-500">
                            Tidak ada data yang cocok dengan pencarian.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($records as $r)
        <x-modal id="detail-prod-{{ $r->id }}" size="md" title="Detail Produksi" :subtitle="$r->id">
            <dl class="text-sm">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">Tanggal</dt>
                    <dd class="font-semibold text-slate-800">{{ FormatHelper::dateId($r->tanggal) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">Nama Produk</dt>
                    <dd class="text-right font-semibold text-slate-800">{{ $r->product_name }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">Jumlah</dt>
                    <dd class="font-semibold text-slate-800">{{ number_format($r->jumlah, 0, ',', '.') }} {{ $r->satuan }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">Status</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-bold {{ $r->status === 'Berhasil' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">{{ $r->status }}</span>
                    </dd>
                </div>
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="shrink-0 text-slate-400">Produk Terdaftar</dt>
                    <dd class="text-right font-semibold text-slate-800">
                        @if ($r->product)
                            {{ $r->product->id }} — {{ $r->product->nama }}
                        @else
                            <span class="text-slate-400">Belum didaftarkan</span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-start justify-between gap-4 py-2.5">
                    <dt class="shrink-0 text-slate-400">Keterangan</dt>
                    <dd class="max-w-[60%] text-right text-slate-700">{{ $r->notes ?: '—' }}</dd>
                </div>
            </dl>
            <div class="mt-4 border-t border-slate-100 pt-4">
                <div class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">Pemakaian Bahan</div>
                @if ($r->materialUsages->isNotEmpty())
                    <div class="max-h-48 overflow-y-auto rounded-lg border border-slate-100">
                        <table class="w-full text-xs">
                            <thead class="sticky top-0 bg-slate-50 text-left text-slate-500">
                                <tr>
                                    <th class="px-3 py-2 font-bold">Bahan Baku</th>
                                    <th class="px-3 py-2 font-bold">Takaran</th>
                                    <th class="px-3 py-2 font-bold">Harga (rata-rata tertimbang)</th>
                                    <th class="px-3 py-2 font-bold">Total Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($r->materialUsages as $usage)
                                    <tr>
                                        <td class="px-3 py-2 font-semibold text-slate-800">{{ $usage->rawMaterial?->nama ?? $usage->raw_material_id }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ FormatHelper::formatQtyOne($usage->jumlah) }} {{ $usage->satuan }}</td>
                                        <td class="px-3 py-2 text-slate-700">{{ FormatHelper::rupiah($usage->harga_satuan) }}</td>
                                        <td class="px-3 py-2 font-semibold text-slate-800">{{ FormatHelper::rupiah($usage->total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex items-center justify-between rounded-lg bg-amber-50 px-3 py-2 text-sm">
                        <span class="font-semibold text-amber-800">Total bahan baku</span>
                        <span class="font-extrabold text-amber-700">{{ FormatHelper::rupiah($r->total_material_cost) }}</span>
                    </div>
                @else
                    <p class="text-sm text-slate-500">Belum ada pemakaian bahan tercatat.</p>
                @endif
            </div>
            <div class="mt-4 flex justify-end border-t border-slate-100 pt-3">
                <button type="button" class="bakery-btn-ghost text-sm" data-modal-close>Tutup</button>
            </div>
        </x-modal>
    @endforeach

    @if ($canEdit ?? true)
        @foreach ($records as $r)
            @php
                $editMaterialRows = old('materials');
                if ($editMaterialRows === null) {
                    $editMaterialRows = $r->materialUsages->map(fn ($u) => [
                        'raw_material_id' => $u->raw_material_id,
                        'jumlah' => FormatHelper::formatQtyInput($u->jumlah),
                    ])->values()->all();
                }
            @endphp
            <x-modal id="edit-prod-{{ $r->id }}" size="lg" title="Ubah Produksi" :subtitle="$r->id" :scrollable="true">
                <form id="form-edit-prod-{{ $r->id }}" method="POST" action="{{ route($updateRoute, $r->id) }}" data-modal-form data-production-form>
                    @csrf @method('PUT')
                    @include('partials.production-form-body', [
                        'prefix' => $r->id,
                        'recordId' => $r->id,
                        'tanggal' => $r->tanggal->format('Y-m-d'),
                        'productName' => $r->product_name,
                        'jumlah' => $r->jumlah,
                        'satuan' => $r->satuan,
                        'status' => $r->status,
                        'notes' => $r->notes,
                        'materials' => $materials,
                        'materialRows' => $editMaterialRows,
                        'linkedProductId' => $r->product?->id,
                        'autofocus' => true,
                    ])
                </form>
                <x-slot:footer>
                    <x-form-actions :form="'form-edit-prod-'.$r->id" compact />
                </x-slot:footer>
            </x-modal>
        @endforeach
    @endif

    @if ($canAdd ?? true)
        <x-modal
            id="prod-baru"
            size="lg"
            title="Tambah Produksi"
            subtitle="Catat hasil produksi dan pemakaian bahan baku"
            :scrollable="true"
            :auto-open="$errors->has('tanggal') || $errors->has('product_name') || $errors->has('jumlah') || $errors->has('status') || $errors->has('notes') || $errors->has('materials') || $errors->has('materials.*')"
        >
            <form id="form-prod-baru" method="POST" action="{{ route($storeRoute) }}" data-modal-form data-production-form>
                @csrf
                @include('partials.production-form-body', [
                    'prefix' => 'create',
                    'tanggal' => date('Y-m-d'),
                    'productName' => '',
                    'jumlah' => '',
                    'satuan' => '',
                    'status' => 'Berhasil',
                    'notes' => '',
                    'materials' => $materials,
                    'materialRows' => old('materials', [['raw_material_id' => '', 'jumlah' => '']]),
                    'autofocus' => true,
                ])
            </form>
            <x-slot:footer>
                <x-form-actions form="form-prod-baru" compact />
            </x-slot:footer>
        </x-modal>
    @endif
</div>
