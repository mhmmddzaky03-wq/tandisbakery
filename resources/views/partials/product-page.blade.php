@php
    use App\Http\Controllers\ProductController;
    use App\Support\FormatHelper;
@endphp
<div>
    <div class="bakery-card" data-table-search>
        <div class="bakery-card-header flex items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div class="min-w-0 shrink text-lg font-extrabold text-slate-900">{{ __('page.product_list_title') }}</div>
            <x-table-search
                :placeholder="__('page.search_product')"
                :value="$search ?? ''"
            />
        </div>

        <div class="bakery-card-body bakery-table-wrap pt-4">
            <table class="bakery-table">
                <thead>
                    <tr>
                        <th class="w-[90px]">{{ __('page.id') }}</th>
                        <th>{{ __('page.production_source') }}</th>
                        <th>{{ __('page.product_name') }}</th>
                        <th class="w-[100px]">{{ __('page.unit') }}</th>
                        <th class="w-[130px]">{{ __('page.price') }}</th>
                        <th class="w-[110px]">{{ __('page.status') }}</th>
                        @if ($canEdit ?? true)
                            <th class="w-[90px] text-center">{{ __('page.action') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody data-table-search-body>
                    @forelse ($products as $product)
                        <tr
                            data-searchable-row
                            data-search="{{ strtolower($product->id.' '.$product->nama.' '.$product->satuan.' '.$product->status.' '.($product->productionRecord?->id ?? '')) }}"
                        >
                            <td class="font-bold text-slate-800">{{ $product->id }}</td>
                            <td>
                                @if ($product->productionRecord)
                                    <div class="font-semibold text-slate-700">{{ $product->productionRecord->id }}</div>
                                    <div class="text-xs text-slate-400">{{ FormatHelper::dateId($product->productionRecord->tanggal) }}</div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td>{{ $product->nama }}</td>
                            <td>{{ $product->satuan }}</td>
                            <td class="font-extrabold text-amber-600">{{ FormatHelper::rupiah($product->harga) }}</td>
                            <td>
                                <span class="bakery-badge {{ $product->status === 'Aktif' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $product->status }}
                                </span>
                            </td>
                            @if ($canEdit ?? true)
                                <td>
                                    <div class="flex items-center justify-center gap-1">
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                                            data-modal-open="edit-produk-{{ $product->id }}"
                                            title="{{ __('ui.edit') }}"
                                            aria-label="{{ __('ui.edit') }}"
                                        >
                                            <x-icons.pencil />
                                        </button>
                                        <form method="POST" action="{{ route($destroyRoute, $product->id) }}" class="inline" onsubmit="return confirm('{{ __('ui.confirm_delete_product') }}')">
                                            @csrf @method('DELETE')
                                            <button
                                                type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                                title="{{ __('ui.delete') }}"
                                                aria-label="{{ __('ui.delete') }}"
                                            >
                                                <x-icons.trash />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr data-table-empty>
                            <td colspan="{{ ($canEdit ?? true) ? 7 : 6 }}" class="px-4 py-12 text-center text-sm text-slate-500">
                                {{ __('page.product_empty') }}
                            </td>
                        </tr>
                    @endforelse
                    <tr data-table-no-results class="hidden">
                        <td colspan="{{ ($canEdit ?? true) ? 7 : 6 }}" class="px-4 py-12 text-center text-sm text-slate-500">
                            {{ __('ui.no_search_results') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if ($canEdit ?? true)
        @foreach ($products as $product)
            @php $editProductions = ProductController::productionsForProduct($product); @endphp
            <x-modal id="edit-produk-{{ $product->id }}" :title="__('page.edit_product')" :subtitle="$product->id">
                <form method="POST" action="{{ route($updateRoute, $product->id) }}" class="space-y-4" data-modal-form data-production-select-form>
                    @csrf @method('PUT')
                    <x-form-field :label="__('page.id')" name="id_display" type="text" :value="$product->id" disabled />
                    <x-form-field :label="__('page.select_production')" name="production_record_id" type="select" required :helper="__('page.select_production_helper')">
                        <option value="">{{ __('page.select_production_placeholder') }}</option>
                        @foreach ($editProductions as $production)
                            <option
                                value="{{ $production->id }}"
                                data-nama="{{ $production->product_name }}"
                                data-satuan="{{ $production->satuan }}"
                                @selected(old('production_record_id', $product->production_record_id) === $production->id)
                            >
                                {{ $production->id }} — {{ $production->product_name }} — {{ FormatHelper::dateId($production->tanggal) }}
                            </option>
                        @endforeach
                    </x-form-field>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-form-field :label="__('page.product_name')" name="nama_preview" type="text" :value="$product->nama" :readonly="true" class="bg-slate-50" />
                        <x-form-field :label="__('page.unit')" name="satuan_preview" type="text" :value="$product->satuan" :readonly="true" class="bg-slate-50" />
                    </div>
                    <x-form-field :label="__('page.price')" name="harga" type="number" :value="old('harga', $product->harga)" min="0" required />
                    <x-form-field :label="__('page.status')" name="status" type="select" required>
                        <option value="Aktif" @selected(old('status', $product->status) === 'Aktif')>Aktif</option>
                        <option value="Non-Aktif" @selected(old('status', $product->status) === 'Non-Aktif')>Non-Aktif</option>
                    </x-form-field>
                    <x-form-actions />
                </form>
            </x-modal>
        @endforeach

        <x-modal id="produk-baru" :title="__('page.add_data')" :auto-open="$errors->has('production_record_id') || $errors->has('harga')">
            <form method="POST" action="{{ route($storeRoute) }}" class="space-y-4" data-modal-form data-production-select-form>
                @csrf
                <x-form-field :label="__('page.select_production')" name="production_record_id" type="select" required autofocus :helper="__('page.select_production_helper')">
                    <option value="">{{ __('page.select_production_placeholder') }}</option>
                    @foreach ($availableProductions as $production)
                        <option
                            value="{{ $production->id }}"
                            data-nama="{{ $production->product_name }}"
                            data-satuan="{{ $production->satuan }}"
                            @selected(old('production_record_id') === $production->id)
                        >
                            {{ $production->id }} — {{ $production->product_name }} — {{ FormatHelper::dateId($production->tanggal) }}
                        </option>
                    @endforeach
                </x-form-field>
                @if ($availableProductions->isEmpty())
                    <p class="rounded-xl bg-amber-50 px-4 py-3 text-xs font-semibold text-amber-700">{{ __('page.no_production_available') }}</p>
                @endif
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form-field :label="__('page.product_name')" name="nama_preview" type="text" value="" :readonly="true" class="bg-slate-50" placeholder="—" />
                    <x-form-field :label="__('page.unit')" name="satuan_preview" type="text" value="" :readonly="true" class="bg-slate-50" placeholder="—" />
                </div>
                <x-form-field :label="__('page.price')" name="harga" type="number" :value="old('harga')" min="0" required />
                <x-form-field :label="__('page.status')" name="status" type="select" required>
                    <option value="Aktif" @selected(old('status', 'Aktif') === 'Aktif')>Aktif</option>
                    <option value="Non-Aktif" @selected(old('status') === 'Non-Aktif')>Non-Aktif</option>
                </x-form-field>
                <x-form-actions />
            </form>
        </x-modal>
    @endif
</div>
