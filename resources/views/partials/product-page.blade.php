@php
    use App\Support\FormatHelper;

    $showRoute = $showRoute ?? 'admin.produk.show';
@endphp
<div>
    <div class="bakery-card" data-table-search>
        <div class="bakery-card-header bakery-card-header--bordered">
            <div class="bakery-card-header__title">{{ __('app.tables.products_list') }}</div>
            <div class="bakery-card-header__actions">
            <x-table-search
                :placeholder="__('product.search')"
                :value="$search ?? ''"
            />
            </div>
        </div>

        <div class="bakery-card-body bakery-table-wrap pt-2">
            <table class="bakery-table">
                <colgroup>
                    <col class="w-[5.5rem]" />
                    <col />
                    <col class="w-[8.5rem]" />
                    <col class="w-[9rem]" />
                    <col class="w-[7.5rem]" />
                </colgroup>
                <thead>
                    <tr>
                        <th>{{ __('app.common.id') }}</th>
                        <th>{{ __('product.table_product_name') }}</th>
                        <th class="text-right">{{ __('app.common.stock') }}</th>
                        <th class="text-right">{{ __('app.common.price') }}</th>
                        <th class="text-center">{{ __('app.common.action') }}</th>
                    </tr>
                </thead>
                <tbody data-table-search-body>
                    @forelse ($products as $product)
                        <tr
                            data-searchable-row
                            data-search="{{ strtolower($product->id.' '.$product->nama.' '.$product->satuan.' '.$product->jumlah.' '.($product->productionRecord?->id ?? '')) }}"
                        >
                            <td class="whitespace-nowrap font-bold text-slate-800">
                                <a href="{{ route($showRoute, $product->id) }}" class="hover:text-sky-600">{{ $product->id }}</a>
                            </td>
                            <td class="min-w-[10rem]">
                                <a href="{{ route($showRoute, $product->id) }}" class="font-semibold text-slate-800 hover:text-sky-600">{{ $product->nama }}</a>
                                @if ($product->productionRecord)
                                    <div class="mt-0.5 text-[11px] font-semibold text-slate-400">{{ __('product.row_initial_production', ['id' => $product->productionRecord->id]) }}</div>
                                @endif
                            </td>
                            <td class="whitespace-nowrap text-right font-bold tabular-nums text-emerald-700">{{ number_format($product->jumlah, 0, ',', '.') }} {{ $product->satuan }}</td>
                            <td class="whitespace-nowrap text-right font-extrabold tabular-nums text-amber-600">{{ FormatHelper::rupiah($product->harga) }}</td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a
                                        href="{{ route($showRoute, $product->id) }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-sky-50 hover:text-sky-600"
                                        title="{{ __('app.common.detail') }}"
                                        aria-label="{{ __('app.common.detail') }}"
                                    >
                                        <x-icons.info-circle class="h-4 w-4" />
                                    </a>
                                    @if ($canEdit ?? true)
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                                            data-modal-open="edit-produk-{{ $product->id }}"
                                            title="{{ __('app.common.edit') }}"
                                            aria-label="{{ __('app.common.edit') }}"
                                        >
                                            <x-icons.pencil />
                                        </button>
                                        <form method="POST" action="{{ route($destroyRoute, $product->id) }}" class="inline" onsubmit="return confirm(@js(__('product.confirm_delete')))">
                                            @csrf @method('DELETE')
                                            <button
                                                type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                                title="{{ __('app.common.delete') }}"
                                                aria-label="{{ __('app.common.delete') }}"
                                            >
                                                <x-icons.trash />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr data-table-empty>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500">
                                {{ __('product.empty') }}
                            </td>
                        </tr>
                    @endforelse
                    <tr data-table-no-results class="hidden">
                        <td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500">
                            {{ __('app.common.not_found') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if ($canEdit ?? true)
        @php
            $editProductId = old('_edit_id');
            $hasProductErrors = $errors->has('harga');
        @endphp
        @foreach ($products as $product)
            @include('partials.product-edit-modal', [
                'product' => $product,
                'productionBatchCount' => \App\Http\Controllers\ProductController::productionsForProduct($product)->count(),
            ])
        @endforeach

        @php
            $isCreateTarget = ! old('_edit_id') && $hasProductErrors;
            $selectedCreateProductionId = old('production_record_id');
        @endphp
        <x-modal
            id="produk-baru"
            :title="__('product.modal_add')"
            size="lg"
            :scrollable="true"
            :auto-open="$isCreateTarget"
        >
            <form method="POST" action="{{ route($storeRoute) }}" class="space-y-3" data-modal-form data-production-select-form>
                @csrf

                @if ($availableProductions->isEmpty())
                    <div class="rounded-xl bg-amber-50 px-4 py-3 ring-1 ring-amber-100">
                        <p class="text-xs font-semibold text-amber-800">{{ __('product.no_success_production') }}</p>
                        <p class="mt-1 text-[11px] font-semibold text-amber-700/80">{{ __('product.record_production_first') }}</p>
                    </div>
                @endif

                <div class="rounded-xl bg-slate-50/80 p-3 ring-1 ring-slate-100">
                    <p class="mb-2 text-[11px] font-bold uppercase tracking-wide text-slate-500">{{ __('product.section_source') }}</p>
                    <div class="min-w-0">
                        <label for="field-production-create" class="mb-1.5 block text-xs font-bold text-slate-600">
                            {{ __('product.field_production_data') }}
                            <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="field-production-create"
                            name="production_record_id"
                            required
                            autofocus
                            @disabled($availableProductions->isEmpty())
                            class="bakery-input h-11 w-full disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 {{ $errors->has('production_record_id') && $isCreateTarget ? '!ring-2 !ring-rose-400' : '' }}"
                        >
                            <option value="" disabled @selected($selectedCreateProductionId === null || $selectedCreateProductionId === '')>{{ __('product.select_production') }}</option>
                            @foreach ($availableProductions as $production)
                                <option
                                    value="{{ $production->id }}"
                                    data-nama="{{ $production->product_name }}"
                                    data-satuan="{{ $production->satuan }}"
                                    @selected($selectedCreateProductionId === $production->id)
                                >
                                    {{ $production->id }} · {{ $production->product_name }} · {{ FormatHelper::dateId($production->tanggal) }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('production_record_id') && $isCreateTarget)
                            <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('production_record_id') }}</p>
                        @endif
                    </div>

                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <div class="min-w-0">
                            <label for="field-nama-preview-create" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('product.field_product_name') }}</label>
                            <input
                                id="field-nama-preview-create"
                                name="nama_preview"
                                type="text"
                                value="{{ old('nama_preview') }}"
                                readonly
                                placeholder="—"
                                class="bakery-input h-11 w-full bg-white text-slate-700 placeholder:text-slate-400"
                            />
                        </div>
                        <div class="min-w-0">
                            <label for="field-satuan-preview-create" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('product.field_unit') }}</label>
                            <input
                                id="field-satuan-preview-create"
                                name="satuan_preview"
                                type="text"
                                value="{{ old('satuan_preview') }}"
                                readonly
                                placeholder="—"
                                class="bakery-input h-11 w-full bg-white uppercase text-slate-700 placeholder:text-slate-400"
                            />
                        </div>
                    </div>
                    <p class="mt-2 text-[11px] font-semibold text-slate-400">{{ __('product.stock_hint_named') }}</p>
                </div>

                <div class="min-w-0">
                    <label for="field-harga-create" class="mb-1.5 block text-xs font-bold text-slate-600">
                        {{ __('product.field_selling_price') }}
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-11 shrink-0 items-center rounded-lg bg-slate-100 px-3 text-xs font-bold text-slate-600">Rp</span>
                        <input
                            id="field-harga-create"
                            name="harga"
                            type="number"
                            value="{{ old('harga') }}"
                            min="0"
                            required
                            inputmode="numeric"
                            @disabled($availableProductions->isEmpty())
                            class="bakery-input h-11 min-w-0 flex-1 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 {{ $errors->has('harga') && $isCreateTarget ? '!ring-2 !ring-rose-400' : '' }}"
                        />
                    </div>
                    @if ($errors->has('harga') && $isCreateTarget)
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('harga') }}</p>
                    @endif
                </div>

                <x-form-actions compact :submit="__('product.submit_register')" />
            </form>
        </x-modal>
    @endif
</div>
