@php
    use App\Support\FormatHelper;

    $metodeLabel = static fn (string $metode): string => match ($metode) {
        'Cash' => 'Tunai',
        'Transfer' => 'Transfer',
        'Mix' => 'Campuran',
        default => $metode,
    };

    $metodeClass = static fn (string $metode): string => match ($metode) {
        'Cash' => 'bg-emerald-50 text-emerald-700',
        'Transfer' => 'bg-sky-50 text-sky-600',
        default => 'bg-amber-50 text-amber-700',
    };
@endphp
<div>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($stats as $s)
            @php $toneMap = ['green' => 'green', 'blue' => 'blue', 'amber' => 'amber', 'violet' => 'violet']; @endphp
            <x-kpi-card :title="$s['label']" :value="$s['value']" :tone="$toneMap[$s['tone']] ?? 'amber'" :icon="$s['icon'] ?? null" />
        @endforeach
    </div>

    <div class="mt-6 bakery-card" data-table-search>
        <div class="bakery-card-header bakery-card-header--bordered">
            <div class="bakery-card-header__title">Data Transaksi Penjualan</div>
            <div class="bakery-card-header__actions">
            <x-table-search placeholder="Cari Transaksi" :value="''" />
            </div>
        </div>

        <div class="bakery-card-body bakery-table-wrap pt-2">
            <table class="bakery-table">
                <thead>
                    <tr>
                        <th class="w-[110px]">Transaksi</th>
                        <th class="w-[120px]">Tanggal</th>
                        <th class="w-[150px]">Total Penjualan</th>
                        <th class="w-[120px]">Metode Pembayaran</th>
                        <th class="w-[100px]">Jumlah Transaksi</th>
                        @if ($canEdit ?? true)
                            <th class="w-[90px] text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody data-table-search-body>
                    @forelse ($transactions as $t)
                        <tr
                            data-searchable-row
                            data-search="{{ strtolower($t->id.' '.FormatHelper::dateId($t->tanggal).' '.$t->total.' '.$t->metode.' '.$metodeLabel($t->metode).' '.$t->jumlah) }}"
                        >
                            <td class="font-bold text-slate-800">{{ $t->id }}</td>
                            <td>{{ FormatHelper::dateId($t->tanggal) }}</td>
                            <td class="font-extrabold text-emerald-600">{{ FormatHelper::rupiah($t->total) }}</td>
                            <td>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $metodeClass($t->metode) }}">
                                    {{ $metodeLabel($t->metode) }}
                                </span>
                            </td>
                            <td class="font-semibold text-slate-700">{{ number_format($t->jumlah, 0, ',', '.') }}</td>
                            @if ($canEdit ?? true)
                                <td>
                                    <div class="flex items-center justify-center gap-1">
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                                            data-modal-open="edit-sales-{{ $t->id }}"
                                            title="Edit"
                                            aria-label="Edit"
                                        >
                                            <x-icons.pencil />
                                        </button>
                                        <form id="delete-sales-{{ $t->id }}" method="POST" action="{{ route($destroyRoute, $t->id) }}" class="inline">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                            onclick="if (window.confirm('Hapus transaksi penjualan ini?')) document.getElementById('delete-sales-{{ $t->id }}').submit()"
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
                                Belum ada transaksi penjualan.
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

    @if ($canEdit ?? true)
        @php
            $editSalesId = old('_edit_id');
            $hasSalesErrors = $errors->has('tanggal') || $errors->has('total') || $errors->has('metode') || $errors->has('jumlah');
        @endphp
        @foreach ($transactions as $t)
                <x-modal id="edit-sales-{{ $t->id }}" title="Edit Transaksi Penjualan" :subtitle="$t->id" :auto-open="$editSalesId === $t->id && $hasSalesErrors">
                    <form method="POST" action="{{ route($updateRoute, $t->id) }}" class="space-y-4" data-modal-form>
                        @csrf @method('PUT')
                        <input type="hidden" name="_edit_id" value="{{ $t->id }}" />
                    <x-form-field label="Tanggal" name="tanggal" type="date" :value="old('tanggal', $t->tanggal->format('Y-m-d'))" required autofocus />
                    <x-form-field label="Total Penjualan (Rp)" name="total" type="number" :value="old('total', $t->total)" min="0" required helper="Total uang masuk pada hari tersebut" />
                    <x-form-field label="Metode Pembayaran" name="metode" type="select" required>
                        <option value="Cash" @selected(old('metode', $t->metode) === 'Cash')>Tunai</option>
                        <option value="Transfer" @selected(old('metode', $t->metode) === 'Transfer')>Transfer</option>
                        <option value="Mix" @selected(old('metode', $t->metode) === 'Mix')>Campuran</option>
                    </x-form-field>
                    <x-form-field label="Jumlah Transaksi" name="jumlah" type="number" :value="old('jumlah', $t->jumlah)" min="1" required helper="Berapa kali transaksi pada hari tersebut" />
                    <x-form-actions />
                </form>
            </x-modal>
        @endforeach
    @endif

    @if ($canAdd ?? true)
        <x-modal
            id="sales-baru"
            title="Tambah Transaksi Penjualan"
            subtitle="Rekap penjualan harian"
            :auto-open="! old('_edit_id') && ($errors->has('tanggal') || $errors->has('total') || $errors->has('metode') || $errors->has('jumlah'))"
        >
            <form method="POST" action="{{ route($storeRoute) }}" class="space-y-4" data-modal-form>
                @csrf
                <x-form-field label="Tanggal" name="tanggal" type="date" :value="old('tanggal', date('Y-m-d'))" required autofocus />
                <x-form-field label="Total Penjualan (Rp)" name="total" type="number" :value="old('total')" min="0" required helper="Total uang masuk pada hari tersebut" />
                <x-form-field label="Metode Pembayaran" name="metode" type="select" required>
                    <option value="Cash" @selected(old('metode') === 'Cash')>Tunai</option>
                    <option value="Transfer" @selected(old('metode') === 'Transfer')>Transfer</option>
                    <option value="Mix" @selected(old('metode', 'Mix') === 'Mix')>Campuran</option>
                </x-form-field>
                <x-form-field label="Jumlah Transaksi" name="jumlah" type="number" :value="old('jumlah', 1)" min="1" required helper="Berapa kali transaksi pada hari tersebut" />
                <x-form-actions />
            </form>
        </x-modal>
    @endif
</div>
