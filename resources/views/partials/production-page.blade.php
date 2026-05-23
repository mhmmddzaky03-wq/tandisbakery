@php use App\Support\FormatHelper; @endphp
<div class="pt-6">
    <div class="grid gap-4 lg:grid-cols-4">
        @foreach ($stats as $s)
            <div class="bakery-card p-5">
                <div class="text-xs font-bold text-slate-400">{{ $s['label'] }}</div>
                <div class="mt-2 text-3xl font-extrabold">{{ $s['value'] }}</div>
            </div>
        @endforeach
    </div>
    <div class="mt-5 bakery-card">
        <div class="bakery-card-header flex-wrap gap-3">
            <div class="text-lg font-extrabold">{{ __('page.production_list_title') }}</div>
            <form method="GET" class="flex gap-2"><input class="bakery-input" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('page.search_production') }}" /><button class="bakery-btn-ghost">Cari</button></form>
            <button type="button" class="bakery-btn-primary" data-modal-open="prod-baru">{{ __('page.add') }}</button>
        </div>
        <div class="bakery-card-body bakery-table-wrap">
            <table class="bakery-table">
                <thead><tr><th>ID</th><th>Tanggal</th><th>Produk</th><th>Jumlah</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach ($records as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ FormatHelper::dateId($r->tanggal) }}</td>
                            <td>{{ $r->product_name }}</td>
                            <td>{{ $r->jumlah }} {{ $r->satuan }}</td>
                            <td><span class="bakery-badge {{ $r->status === 'Berhasil' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">{{ $r->status }}</span></td>
                            <td>
                                @if ($canEdit ?? true)
                                    <div class="flex gap-2">
                                        <button type="button" class="bakery-btn-ghost text-xs" data-modal-open="edit-prod-{{ $r->id }}">Edit</button>
                                        <form method="POST" action="{{ route($destroyRoute, $r->id) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button type="submit" class="text-xs text-rose-600">Hapus</button></form>
                                    </div>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if ($canEdit ?? true)
        @foreach ($records as $r)
            <x-modal id="edit-prod-{{ $r->id }}" title="Ubah Produksi" :subtitle="$r->id">
                <form method="POST" action="{{ route($updateRoute, $r->id) }}" data-modal-form>
                    @csrf @method('PUT')
                    <x-form-field label="Tanggal produksi" name="tanggal" type="date" :value="$r->tanggal->format('Y-m-d')" required autofocus />
                    <x-form-field label="Nama produk" name="product_name" :value="$r->product_name" required />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-form-field label="Jumlah" name="jumlah" type="number" :value="$r->jumlah" min="0" required />
                        <x-form-field label="Satuan" name="satuan" :value="$r->satuan" required />
                    </div>
                    <x-form-field label="Status" name="status" type="select" required>
                        <option value="Berhasil" @selected($r->status === 'Berhasil')>Berhasil</option>
                        <option value="Gagal" @selected($r->status === 'Gagal')>Gagal</option>
                    </x-form-field>
                    <x-form-field label="Catatan" name="notes" type="textarea" :value="$r->notes" helper="Opsional — alasan jika gagal" />
                    <x-form-actions />
                </form>
            </x-modal>
        @endforeach
    @endif

    <x-modal id="prod-baru" :title="__('nav.input_production')" subtitle="Pilih produk dari daftar atau isi manual" :auto-open="$errors->has('tanggal') || $errors->has('product_name')">
        <form method="POST" action="{{ route($storeRoute) }}" data-modal-form data-product-form>
            @csrf
            <x-form-field label="Tanggal produksi" name="tanggal" type="date" :value="old('tanggal', date('Y-m-d'))" required autofocus />
            <x-form-field label="Produk" name="product_id" type="select" helper="Pilih produk untuk mengisi nama & satuan otomatis">
                <option value="">— Input manual —</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" data-nama="{{ $p->nama }}" data-satuan="{{ $p->satuan }}" @selected(old('product_id') == $p->id)>{{ $p->nama }}</option>
                @endforeach
            </x-form-field>
            <x-form-field label="Jumlah hasil" name="jumlah" type="number" :value="old('jumlah')" min="0" required />
            <x-form-field label="Status" name="status" type="select" required>
                <option value="Berhasil" @selected(old('status', 'Berhasil') === 'Berhasil')>Berhasil</option>
                <option value="Gagal" @selected(old('status') === 'Gagal')>Gagal</option>
            </x-form-field>
            <x-form-field label="Catatan" name="notes" type="textarea" :value="old('notes')" helper="Opsional" />
            <x-form-actions />
        </form>
    </x-modal>
</div>
