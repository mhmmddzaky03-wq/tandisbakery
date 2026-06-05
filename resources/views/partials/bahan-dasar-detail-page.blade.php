@php
    use App\Support\FormatHelper;

    $updateRoute = $updateRoute ?? 'admin.bahan_dasar.update';
    $buatAdonanRoute = $buatAdonanRoute ?? 'admin.bahan_dasar.buat_adonan';
    $destroyBatchRoute = $destroyBatchRoute ?? 'admin.bahan_dasar.batch.destroy';

    $jumlahStok = (float) $item->jumlah;
    $minStok = (float) $item->min;
    $habis = $jumlahStok <= 0;
    $aman = ! $habis && $jumlahStok > $minStok;
    $statusLabel = $habis ? 'Stok Habis' : ($aman ? 'Stok Aman' : 'Perlu Diisi');
    $statusBadgeClass = $habis
        ? 'bg-rose-50 text-rose-600'
        : ($aman ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-700');
    $stockValue = FormatHelper::formatQtyOne($item->jumlah).' '.$satuan;
    $stockSub = $activeBatches->count().' batch aktif · '.$statusLabel;
    $hargaValue = FormatHelper::rupiah($item->harga);
    $hargaSub = 'Per '.$satuan;
    $batchValue = (string) $item->batches_count;
    $batchSub = $activeBatches->count().' batch masih ada sisa';
    $nilaiValue = FormatHelper::rupiah($totalNilai);
    $nilaiSub = 'Stok × harga rata-rata';

    $allPemakaian = $batches->flatMap(fn ($b) => $b->pemakaianBahanBaku)->values();
@endphp

<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-kpi-card title="Stok Saat Ini" :value="$stockValue" :sub="$stockSub" tone="green" icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>' />
        <x-kpi-card title="Harga Rata-rata" :value="$hargaValue" :sub="$hargaSub" tone="amber" icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>' />
        <x-kpi-card title="Total Batch" :value="$batchValue" :sub="$batchSub" tone="blue" icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>' />
        <x-kpi-card title="Total Nilai Stok" :value="$nilaiValue" :sub="$nilaiSub" tone="violet" icon='<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>' />
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header border-b border-slate-100 pb-4">
            <h2 class="text-base font-extrabold text-slate-900">Informasi Bahan Dasar</h2>
        </div>
        <div class="bakery-card-body">
            <dl class="grid gap-x-8 gap-y-0 sm:grid-cols-2">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">ID</dt>
                    <dd class="font-bold text-slate-800">{{ $item->id }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">Nama</dt>
                    <dd class="font-semibold text-slate-800">{{ $item->nama }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">Satuan</dt>
                    <dd class="font-semibold uppercase text-slate-800">{{ $satuan }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 text-sm">
                    <dt class="text-slate-400">Batas Aman</dt>
                    <dd><x-unit-qty :qty="$item->min" :unit="$satuan" qty-class="font-semibold text-slate-800" /></dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3 text-sm sm:col-span-2">
                    <dt class="text-slate-400">Status</dt>
                    <dd><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $statusBadgeClass }}">{{ $statusLabel }}</span></dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header bakery-card-header--bordered">
            <h2 class="text-base font-extrabold text-slate-900">Stok Adonan per Batch</h2>
            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Total {{ FormatHelper::formatQtyOne($item->jumlah) }} {{ $satuan }}</span>
        </div>
        <div class="bakery-card-body overflow-x-auto pt-2">
            @if ($batches->isNotEmpty())
                <table class="bakery-table w-full min-w-[40rem]">
                    <thead>
                        <tr>
                            <th class="w-[7rem]">Tanggal</th>
                            <th class="w-[7rem]">Masuk</th>
                            <th class="w-[7rem]">Sisa</th>
                            <th class="w-[8rem]">Total Biaya</th>
                            <th>Catatan</th>
                            <th class="w-[4rem] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batches as $batch)
                            <tr>
                                <td class="whitespace-nowrap text-slate-700">{{ FormatHelper::dateId($batch->tanggal) }}</td>
                                <td class="font-semibold text-slate-800">{{ FormatHelper::formatQtyOne($batch->jumlah) }} {{ $satuan }}</td>
                                <td class="font-bold text-emerald-700">{{ FormatHelper::formatQtyOne($batch->sisa) }} {{ $satuan }}</td>
                                <td class="text-slate-700">{{ FormatHelper::rupiah($batch->total_biaya) }}</td>
                                <td class="max-w-[12rem] truncate text-slate-500" title="{{ $batch->catatan }}">{{ $batch->catatan ?: '—' }}</td>
                                <td class="text-center">
                                    @if ((float) $batch->sisa >= (float) $batch->jumlah - 0.000_1)
                                        <form method="POST" action="{{ route($destroyBatchRoute, [$item->id, $batch->id]) }}" class="inline" onsubmit="return confirm('Hapus batch ini? Bahan baku akan dikembalikan ke stok.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus batch" aria-label="Hapus batch">
                                                <x-icons.trash class="h-4 w-4" />
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] font-semibold text-slate-400">Terpakai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="py-10 text-center">
                    <p class="text-sm font-semibold text-slate-500">Belum ada adonan dibuat. Klik <strong>Buat Adonan</strong> untuk memulai.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header bakery-card-header--bordered">
            <h2 class="text-base font-extrabold text-slate-900">Dipakai di Produksi</h2>
            <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-bold text-sky-700">{{ ($produksiTerpakai ?? collect())->count() }} produksi</span>
        </div>
        <div class="bakery-card-body overflow-x-auto pt-2">
            @if (($produksiTerpakai ?? collect())->isNotEmpty())
                <table class="bakery-table w-full min-w-[44rem]">
                    <thead>
                        <tr>
                            <th class="w-[90px]">ID</th>
                            <th class="w-[7rem]">Tanggal</th>
                            <th>Produk</th>
                            <th class="w-[7rem] text-center">Status</th>
                            <th class="min-w-[8.5rem]">Takaran</th>
                            <th class="w-[7rem] text-right">Biaya</th>
                            <th class="w-[4rem] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produksiTerpakai as $entry)
                            @php
                                $record = $entry->record;
                                $statusClass = $record->status === 'Berhasil'
                                    ? 'bg-emerald-50 text-emerald-600'
                                    : 'bg-rose-50 text-rose-600';
                            @endphp
                            <tr>
                                <td class="font-bold text-slate-800">{{ $record->id }}</td>
                                <td class="whitespace-nowrap text-slate-700">{{ FormatHelper::dateId($record->tanggal) }}</td>
                                <td class="font-semibold text-slate-800">{{ $record->product_name }}</td>
                                <td class="text-center">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $record->status }}</span>
                                </td>
                                <td><x-unit-qty :qty="$entry->total_qty" :unit="$entry->satuan" qty-class="font-semibold text-slate-800" anchor-toggle /></td>
                                <td class="text-right font-bold text-slate-800">{{ FormatHelper::rupiah($entry->total_biaya) }}</td>
                                <td class="text-center">
                                    <a
                                        href="{{ route('admin.produksi.show', $record->id) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-sky-50 hover:text-sky-600"
                                        title="Detail produksi"
                                        aria-label="Detail produksi"
                                    >
                                        <x-icons.info-circle class="h-4 w-4" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="py-10 text-center">
                    <p class="text-sm font-semibold text-slate-500">Bahan dasar ini belum pernah dipakai di produksi.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bakery-card">
        <div class="bakery-card-header bakery-card-header--bordered">
            <h2 class="text-base font-extrabold text-slate-900">Riwayat Pemakaian Bahan Baku</h2>
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ $allPemakaian->count() }} baris</span>
        </div>
        <div class="bakery-card-body overflow-x-auto pt-2">
            @if ($allPemakaian->isNotEmpty())
                <table class="bakery-table w-full min-w-[48rem]">
                    <thead>
                        <tr>
                            <th class="w-[7rem]">Tanggal</th>
                            <th class="w-[8rem]">Batch Adonan</th>
                            <th>Bahan Baku</th>
                            <th class="min-w-[8.5rem]">Takaran</th>
                            <th class="w-[7rem] text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batches as $batch)
                            @foreach ($batch->pemakaianBahanBaku as $usage)
                                <tr>
                                    <td class="whitespace-nowrap text-slate-700">{{ FormatHelper::dateId($batch->tanggal) }}</td>
                                    <td class="text-sm font-semibold text-slate-700">#{{ $batch->id }}</td>
                                    <td class="font-semibold text-slate-800">{{ $usage->bahanBaku?->nama ?? $usage->raw_material_id }}</td>
                                    <td><x-unit-qty :qty="$usage->jumlah" :unit="$usage->satuan" qty-class="font-semibold text-slate-800" anchor-toggle /></td>
                                    <td class="text-right font-bold text-slate-800">{{ FormatHelper::rupiah($usage->total) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-amber-50/60">
                            <td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-amber-900">Total biaya bahan baku</td>
                            <td class="px-4 py-3 text-right text-sm font-extrabold text-amber-800">{{ FormatHelper::rupiah($totalBiayaBatch) }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="py-10 text-center">
                    <p class="text-sm font-semibold text-slate-500">Belum ada pemakaian bahan baku untuk adonan ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@include('partials.bahan-dasar-action-modals')
