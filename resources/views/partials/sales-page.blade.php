@php use App\Support\FormatHelper; @endphp
<div class="pt-6">
    <div class="bakery-card">
        <div class="bakery-card-header">
            <div class="text-lg font-extrabold">{{ __('page.sales_list_title') }}</div>
            <button type="button" class="bakery-btn-primary" data-modal-open="sales-baru">{{ __('page.add_transaction') }}</button>
        </div>
        <div class="bakery-card-body">
            <div class="mb-5 grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl bg-slate-50 p-4"><div class="text-xs text-slate-400">{{ __('page.today_sales') }}</div><div class="mt-1 text-2xl font-extrabold text-emerald-600">{{ FormatHelper::rupiah($todaySales) }}</div></div>
                <div class="rounded-2xl bg-slate-50 p-4"><div class="text-xs text-slate-400">{{ __('page.today_transactions') }}</div><div class="mt-1 text-2xl font-extrabold text-sky-600">{{ $todayCount }}</div></div>
                <div class="rounded-2xl bg-slate-50 p-4"><div class="text-xs text-slate-400">Total data</div><div class="mt-1 text-2xl font-extrabold text-amber-600">{{ $transactions->count() }}</div></div>
            </div>
            <form method="GET" class="mb-4 flex gap-2"><input class="bakery-input flex-1" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('page.search_trx') }}" /><button class="bakery-btn-ghost">Cari</button></form>
            <div class="bakery-table-wrap">
                <table class="bakery-table">
                    <thead><tr><th>ID</th><th>Tanggal</th><th>Total</th><th>Metode</th><th>Jumlah trx</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($transactions as $t)
                            <tr>
                                <td>{{ $t->id }}</td>
                                <td>{{ FormatHelper::dateId($t->tanggal) }}</td>
                                <td class="font-extrabold text-emerald-600">{{ FormatHelper::rupiah($t->total) }}</td>
                                <td>{{ $t->metode }}</td>
                                <td>{{ $t->jumlah }}</td>
                                <td><button type="button" class="bakery-btn-ghost text-xs" data-modal-open="edit-sales-{{ $t->id }}">Edit</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($transactions as $t)
        <x-modal id="edit-sales-{{ $t->id }}" title="Ubah Transaksi" :subtitle="$t->id">
            <form method="POST" action="{{ route($updateRoute, $t->id) }}" data-modal-form>
                @csrf @method('PUT')
                <x-form-field label="Tanggal" name="tanggal" type="date" :value="$t->tanggal->format('Y-m-d')" required autofocus />
                <x-form-field label="Total penjualan (Rp)" name="total" type="number" :value="$t->total" min="0" required helper="Total uang masuk pada hari ini" />
                <x-form-field label="Metode pembayaran" name="metode" type="select" required>
                    <option value="Cash" @selected($t->metode === 'Cash')>Cash</option>
                    <option value="Transfer" @selected($t->metode === 'Transfer')>Transfer</option>
                    <option value="Mix" @selected($t->metode === 'Mix')>Mix (Campuran)</option>
                </x-form-field>
                <x-form-field label="Jumlah struk/transaksi" name="jumlah" type="number" :value="$t->jumlah" min="1" required helper="Berapa kali transaksi pada hari tersebut" />
                <x-form-actions />
            </form>
        </x-modal>
    @endforeach

    <x-modal id="sales-baru" title="Tambah Rekap Penjualan" subtitle="Ringkasan penjualan harian" :auto-open="$errors->has('tanggal') || $errors->has('total')">
        <form method="POST" action="{{ route($storeRoute) }}" data-modal-form>
            @csrf
            <x-form-field label="Tanggal" name="tanggal" type="date" :value="old('tanggal', date('Y-m-d'))" required autofocus />
            <x-form-field label="Total penjualan (Rp)" name="total" type="number" :value="old('total')" min="0" required />
            <x-form-field label="Metode pembayaran" name="metode" type="select" required>
                <option value="Cash" @selected(old('metode') === 'Cash')>Cash</option>
                <option value="Transfer" @selected(old('metode') === 'Transfer')>Transfer</option>
                <option value="Mix" @selected(old('metode', 'Mix') === 'Mix')>Mix (Campuran)</option>
            </x-form-field>
            <x-form-field label="Jumlah struk/transaksi" name="jumlah" type="number" :value="old('jumlah', 1)" min="1" required />
            <x-form-actions />
        </form>
    </x-modal>
</div>
