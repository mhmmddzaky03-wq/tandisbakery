@php use App\Support\FormatHelper; @endphp
<div class="pt-6">
    <div class="bakery-card">
        <div class="bakery-card-header flex-wrap gap-3">
            <div>
                <div class="text-lg font-extrabold">{{ __('page.stock_list_title') }}</div>
                <div class="mt-1 text-sm text-slate-400">{{ __('page.stock_list_subtitle') }}</div>
            </div>
            <form method="GET" class="flex flex-1 min-w-[200px] max-w-sm gap-2">
                <input class="bakery-input flex-1" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('page.search_stock') }}" aria-label="{{ __('page.search_stock') }}" />
                <button type="submit" class="bakery-btn-ghost shrink-0">{{ __('ui.search') }}</button>
            </form>
            <button type="button" class="bakery-btn-primary shrink-0" data-modal-open="stok-baru">+ {{ __('page.add') }}</button>
        </div>
        <div class="bakery-card-body bakery-table-wrap">
            <table class="bakery-table">
                <thead>
                    <tr>
                        <th>{{ __('page.id') }}</th><th>{{ __('page.name') }}</th><th>{{ __('page.quantity') }}</th>
                        <th>{{ __('page.min_threshold') }}</th><th>{{ __('page.unit_price') }}</th><th>{{ __('page.status') }}</th><th>{{ __('page.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materials as $m)
                        @php $aman = (float) $m->jumlah > (float) $m->min; @endphp
                        <tr>
                            <td class="font-bold">{{ $m->id }}</td>
                            <td>{{ $m->nama }}</td>
                            <td>{{ $m->jumlah }} kg</td>
                            <td>{{ $m->min }} kg</td>
                            <td>{{ FormatHelper::rupiah($m->harga) }}</td>
                            <td><span class="bakery-badge {{ $aman ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-700' }}">{{ $aman ? 'Stok aman' : 'Perlu diisi' }}</span></td>
                            <td><button type="button" class="bakery-btn-ghost text-xs" data-modal-open="edit-stok-{{ $m->id }}">Edit</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($materials as $m)
        <x-modal id="edit-stok-{{ $m->id }}" title="Ubah Stok" :subtitle="$m->nama">
            <form method="POST" action="{{ route($updateRoute, $m->id) }}" data-modal-form>
                @csrf @method('PUT')
                <x-form-field label="Nama bahan" name="nama" :value="$m->nama" required autofocus />
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form-field label="Jumlah stok (kg)" name="jumlah" type="number" :value="$m->jumlah" step="0.0001" min="0" required />
                    <x-form-field label="Batas minimum (kg)" name="min" type="number" :value="$m->min" step="0.0001" min="0" required helper="Jika stok ≤ batas ini, status jadi perlu diisi" />
                </div>
                <x-form-field label="Harga per kg (Rp)" name="harga" type="number" :value="$m->harga" min="0" required />
                <x-form-actions />
            </form>
        </x-modal>
    @endforeach

    <x-modal id="stok-baru" title="Tambah Bahan Baku" subtitle="Catat bahan baku baru untuk produksi" :auto-open="$errors->has('id') || $errors->has('nama')">
        <form method="POST" action="{{ route($storeRoute) }}" data-modal-form>
            @csrf
            <x-form-field label="Kode bahan" name="id" :value="old('id')" required autofocus placeholder="SBB011" helper="Kode unik bahan baku" />
            <x-form-field label="Nama bahan" name="nama" :value="old('nama')" required />
            <div class="grid gap-4 sm:grid-cols-2">
                <x-form-field label="Jumlah awal (kg)" name="jumlah" type="number" :value="old('jumlah', 0)" step="0.0001" min="0" required />
                <x-form-field label="Batas minimum (kg)" name="min" type="number" :value="old('min', 0)" step="0.0001" min="0" required />
            </div>
            <x-form-field label="Harga per kg (Rp)" name="harga" type="number" :value="old('harga')" min="0" required />
            <x-form-actions />
        </form>
    </x-modal>
</div>
