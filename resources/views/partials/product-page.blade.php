@php use App\Support\FormatHelper; @endphp
<div class="pt-6">
    <div class="bakery-card">
        <div class="bakery-card-header">
            <div>
                <div class="text-lg font-extrabold text-slate-900">{{ __('page.product_list_title') }}</div>
                <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.product_list_subtitle') }}</div>
            </div>
            <button class="bakery-btn-primary" type="button" data-modal-open="produk-baru">{{ __('page.add_product') }}</button>
        </div>
        <div class="bakery-card-body bakery-table-wrap">
            <table class="bakery-table">
                <thead>
                    <tr>
                        <th>{{ __('page.id') }}</th>
                        <th>{{ __('page.product_name') }}</th>
                        <th>{{ __('page.unit') }}</th>
                        <th>{{ __('page.price') }}</th>
                        <th>{{ __('page.status') }}</th>
                        <th>{{ __('page.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="font-bold">{{ $product->id }}</td>
                            <td>{{ $product->nama }}</td>
                            <td>{{ $product->satuan }}</td>
                            <td class="font-extrabold text-amber-600">{{ FormatHelper::rupiah($product->harga) }}</td>
                            <td><span class="bakery-badge {{ $product->status === 'Aktif' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">{{ $product->status }}</span></td>
                            <td>
                                <div class="flex gap-2">
                                    <button type="button" class="bakery-btn-ghost px-2 py-1 text-xs" data-modal-open="edit-produk-{{ $product->id }}">Edit</button>
                                    <form method="POST" action="{{ route($destroyRoute, $product->id) }}" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-rose-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-slate-500">{{ __('ui.empty_data') ?? 'Belum ada data.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($products as $product)
        <x-modal id="edit-produk-{{ $product->id }}" title="Ubah Produk" :subtitle="$product->id">
            <form method="POST" action="{{ route($updateRoute, $product->id) }}" class="space-y-1" data-modal-form>
                @csrf @method('PUT')
                <x-form-field :label="__('page.product_name')" name="nama" :value="$product->nama" required autofocus />
                <x-form-field :label="__('page.unit')" name="satuan" :value="$product->satuan" required :helper="__('ui.unit_helper') ?? 'Contoh: pcs, loyang, kg'" />
                <x-form-field :label="__('page.price')" name="harga" type="number" :value="$product->harga" min="0" required :helper="__('ui.price_helper') ?? 'Harga jual per satuan (Rp)'" />
                <x-form-field :label="__('page.status')" name="status" type="select" required>
                    <option value="Aktif" @selected($product->status === 'Aktif')>Aktif</option>
                    <option value="Non-Aktif" @selected($product->status === 'Non-Aktif')>Non-Aktif</option>
                </x-form-field>
                <x-form-actions />
            </form>
        </x-modal>
    @endforeach

    <x-modal id="produk-baru" :title="__('page.add_product')" subtitle="Lengkapi field bertanda *" :auto-open="$errors->has('id') || $errors->has('nama')">
        <form method="POST" action="{{ route($storeRoute) }}" class="space-y-1" data-modal-form>
            @csrf
            <x-form-field :label="__('page.id')" name="id" :value="old('id')" required autofocus :helper="__('ui.product_id_helper') ?? 'Kode unik, contoh: P004'" placeholder="P004" />
            <x-form-field :label="__('page.product_name')" name="nama" :value="old('nama')" required />
            <x-form-field :label="__('page.unit')" name="satuan" :value="old('satuan')" required placeholder="loyang" />
            <x-form-field :label="__('page.price')" name="harga" type="number" :value="old('harga')" min="0" required />
            <x-form-field :label="__('page.status')" name="status" type="select" required>
                <option value="Aktif" @selected(old('status', 'Aktif') === 'Aktif')>Aktif</option>
                <option value="Non-Aktif" @selected(old('status') === 'Non-Aktif')>Non-Aktif</option>
            </x-form-field>
            <x-form-actions />
        </form>
    </x-modal>
</div>
