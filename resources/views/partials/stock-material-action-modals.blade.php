@php
    use App\Models\RawMaterial;
    use App\Support\FormatHelper;

    $selectedSatuan = old('satuan', $m->satuan ?? '');
    $unitNames = $units->pluck('nama');
    $editMin = old('min') !== null ? old('min') : FormatHelper::formatQtyInput($m->min);
    $isEditTarget = old('_edit_id') === $m->id;
    $latestRestock = $m->latestRestock();
    $editKodeProduksi = old('kode_produksi', $latestRestock?->kode_produksi ?? '');
    $editExpired = old('expired', $latestRestock?->expired?->format('Y-m-d') ?? '');

    $satuan = $m->satuan ?? 'kg';
    $isRestockTarget = old('_restock_id') === $m->id;
    $restockTanggal = old('restock_tanggal', now()->toDateString());
    $restockJumlah = old('restock_jumlah', '');
    $restockHarga = old('restock_harga', '');
    $restockKodeProduksi = old('restock_kode_produksi', '');
    $restockExpired = old('restock_expired', '');
    $restockCatatan = old('restock_catatan', '');
@endphp

<x-modal
    id="edit-stok-{{ $m->id }}"
    :title="__('stock.modal_edit')"
    size="lg"
    :scrollable="true"
    :auto-open="$isEditTarget && ($errors->has('nama') || $errors->has('kategori') || $errors->has('satuan') || $errors->has('min') || $errors->has('kode_produksi') || $errors->has('expired'))"
>
    <form method="POST" action="{{ route($updateRoute, $m->id) }}" class="space-y-3" data-modal-form data-stock-form>
        @csrf @method('PUT')
        <input type="hidden" name="_edit_id" value="{{ $m->id }}" />
        <div class="grid gap-3 sm:grid-cols-2 [&_.bakery-field+.bakery-field]:!mt-0">
            <x-form-field :label="__('stock.field_name')" name="nama" :value="old('nama', $m->nama)" required autofocus />
            <x-form-field :label="__('stock.field_category')" name="kategori" type="select" :value="old('kategori', $m->kategori ?? RawMaterial::KATEGORI_PADAT)" required>
                @foreach (RawMaterial::kategoriOptions() as $value => $label)
                    <option value="{{ $value }}" @selected(old('kategori', $m->kategori ?? RawMaterial::KATEGORI_PADAT) === $value)>{{ $label }}</option>
                @endforeach
            </x-form-field>
        </div>
        <p class="text-xs text-slate-500">{{ __('stock.restock_hint') }}</p>
        <div class="grid gap-3 sm:grid-cols-2 [&_.bakery-field+.bakery-field]:!mt-0">
            <div class="min-w-0">
                <label for="field-satuan-edit-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                    {{ __('app.common.unit') }}
                    <span class="text-rose-500" aria-hidden="true">*</span>
                </label>
                <select
                    id="field-satuan-edit-{{ $m->id }}"
                    name="satuan"
                    required
                    class="bakery-input h-11 w-full {{ $errors->has('satuan') && $isEditTarget ? '!ring-2 !ring-rose-400' : '' }}"
                >
                    <option value="" disabled @selected($selectedSatuan === '')>{{ __('stock.select_unit') }}</option>
                    @if ($selectedSatuan && ! $unitNames->contains($selectedSatuan))
                        <option value="{{ $selectedSatuan }}" @selected(true)>{{ $selectedSatuan }}</option>
                    @endif
                    @foreach ($units as $unit)
                        <option value="{{ $unit->nama }}" @selected($selectedSatuan === $unit->nama)>{{ $unit->nama }}</option>
                    @endforeach
                </select>
                @if ($errors->has('satuan') && $isEditTarget)
                    <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('satuan') }}</p>
                @endif
            </div>
            <div class="bakery-field !mt-0">
                <label for="field-min-edit-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                    {{ __('stock.field_min') }}
                    <span class="text-rose-500" aria-hidden="true">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <input
                        id="field-min-edit-{{ $m->id }}"
                        name="min"
                        type="text"
                        value="{{ $editMin }}"
                        inputmode="decimal"
                        autocomplete="off"
                        data-decimal-one
                        required
                        class="bakery-input h-11 flex-1 {{ $errors->has('min') && $isEditTarget ? '!ring-2 !ring-rose-400' : '' }}"
                    />
                    <span
                        data-stock-unit-suffix
                        class="inline-flex h-11 min-w-[3rem] shrink-0 items-center justify-center rounded-lg bg-slate-100 px-2.5 text-xs font-bold uppercase text-slate-600"
                    >{{ $selectedSatuan ?: '—' }}</span>
                </div>
                @if ($errors->has('min') && $isEditTarget)
                    <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('min') }}</p>
                @endif
            </div>
        </div>
        @if ($latestRestock)
            <div class="rounded-xl bg-slate-50/80 p-3 ring-1 ring-slate-100">
                <p class="mb-2 text-[11px] font-bold uppercase tracking-wide text-slate-500">{{ __('stock.last_restock_batch') }}</p>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="min-w-0">
                        <label for="field-kode-edit-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                            {{ __('stock.field_production_code') }}
                            <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="field-kode-edit-{{ $m->id }}"
                            name="kode_produksi"
                            type="text"
                            value="{{ $editKodeProduksi }}"
                            placeholder="{{ __('stock.production_code_placeholder') }}"
                            required
                            class="bakery-input h-11 w-full {{ $errors->has('kode_produksi') && $isEditTarget ? '!ring-2 !ring-rose-400' : '' }}"
                        />
                        @if ($errors->has('kode_produksi') && $isEditTarget)
                            <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('kode_produksi') }}</p>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <label for="field-expired-edit-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                            {{ __('stock.field_expired') }}
                            <span class="text-rose-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="field-expired-edit-{{ $m->id }}"
                            name="expired"
                            type="date"
                            value="{{ $editExpired }}"
                            required
                            class="bakery-input h-11 w-full {{ $errors->has('expired') && $isEditTarget ? '!ring-2 !ring-rose-400' : '' }}"
                        />
                        @if ($errors->has('expired') && $isEditTarget)
                            <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('expired') }}</p>
                        @endif
                    </div>
                </div>
                <p class="mt-2 text-[11px] font-semibold text-slate-400">{{ __('stock.batch_edit_hint', ['date' => FormatHelper::dateId($latestRestock->tanggal)]) }}</p>
            </div>
        @endif
        <x-form-actions compact />
    </form>
</x-modal>

<x-modal
    id="restock-stok-{{ $m->id }}"
    :title="__('stock.modal_restock')"
    :scrollable="true"
    :auto-open="$isRestockTarget && ($errors->has('restock_tanggal') || $errors->has('restock_jumlah') || $errors->has('restock_harga') || $errors->has('restock_kode_produksi') || $errors->has('restock_expired') || $errors->has('restock_catatan'))"
>
    <form method="POST" action="{{ route($restockRoute, $m->id) }}" class="space-y-4" data-modal-form>
        @csrf
        <input type="hidden" name="_restock_id" value="{{ $m->id }}" />
        <div class="rounded-lg bg-slate-50 px-3 py-2.5 text-sm">
            <div class="flex items-center justify-between gap-4">
                <span class="min-w-0 truncate font-bold text-slate-800">{{ $m->nama }}</span>
                <div class="flex shrink-0 items-center gap-2">
                    <span class="text-slate-500">{{ __('stock.field_current_stock') }}</span>
                    <span class="font-bold text-slate-800">{{ FormatHelper::formatQtyOne($m->jumlah) }} {{ $satuan }}</span>
                </div>
            </div>
        </div>
        <div class="bakery-field">
            <label for="field-restock-tanggal-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                {{ __('stock.field_purchase_date') }}
                <span class="text-rose-500" aria-hidden="true">*</span>
            </label>
            <input
                id="field-restock-tanggal-{{ $m->id }}"
                name="restock_tanggal"
                type="date"
                value="{{ $restockTanggal }}"
                required
                class="bakery-input h-11 w-full {{ $errors->has('restock_tanggal') && $isRestockTarget ? '!ring-2 !ring-rose-400' : '' }}"
            />
            @if ($errors->has('restock_tanggal') && $isRestockTarget)
                <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('restock_tanggal') }}</p>
            @endif
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            <div class="min-w-0">
                <label for="field-restock-kode-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                    {{ __('stock.field_production_code') }}
                    <span class="text-rose-500" aria-hidden="true">*</span>
                </label>
                <input
                    id="field-restock-kode-{{ $m->id }}"
                    name="restock_kode_produksi"
                    type="text"
                    value="{{ $restockKodeProduksi }}"
                    placeholder="{{ __('stock.production_code_placeholder') }}"
                    required
                    class="bakery-input h-11 w-full {{ $errors->has('restock_kode_produksi') && $isRestockTarget ? '!ring-2 !ring-rose-400' : '' }}"
                />
                @if ($errors->has('restock_kode_produksi') && $isRestockTarget)
                    <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('restock_kode_produksi') }}</p>
                @endif
            </div>
            <div class="min-w-0">
                <label for="field-restock-expired-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                    {{ __('stock.field_expired') }}
                    <span class="text-rose-500" aria-hidden="true">*</span>
                </label>
                <input
                    id="field-restock-expired-{{ $m->id }}"
                    name="restock_expired"
                    type="date"
                    value="{{ $restockExpired }}"
                    required
                    class="bakery-input h-11 w-full {{ $errors->has('restock_expired') && $isRestockTarget ? '!ring-2 !ring-rose-400' : '' }}"
                />
                @if ($errors->has('restock_expired') && $isRestockTarget)
                    <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('restock_expired') }}</p>
                @endif
            </div>
        </div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <div class="min-w-0 flex-1">
                <label for="field-restock-jumlah-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                    {{ __('stock.field_restock_qty') }}
                    <span class="text-rose-500" aria-hidden="true">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <input
                        id="field-restock-jumlah-{{ $m->id }}"
                        name="restock_jumlah"
                        type="text"
                        value="{{ $restockJumlah }}"
                        inputmode="decimal"
                        autocomplete="off"
                        data-decimal-one
                        required
                        class="bakery-input h-11 flex-1 {{ $errors->has('restock_jumlah') && $isRestockTarget ? '!ring-2 !ring-rose-400' : '' }}"
                    />
                    <span class="inline-flex h-11 min-w-[3rem] shrink-0 items-center justify-center rounded-lg bg-slate-100 px-2.5 text-xs font-bold uppercase text-slate-600">{{ $satuan }}</span>
                </div>
                @if ($errors->has('restock_jumlah') && $isRestockTarget)
                    <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('restock_jumlah') }}</p>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <label for="field-restock-harga-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                    {{ __('stock.field_price_per') }} {{ $satuan }}
                    <span class="text-rose-500" aria-hidden="true">*</span>
                </label>
                <input
                    id="field-restock-harga-{{ $m->id }}"
                    name="restock_harga"
                    type="number"
                    value="{{ $restockHarga }}"
                    min="1"
                    required
                    class="bakery-input h-11 w-full {{ $errors->has('restock_harga') && $isRestockTarget ? '!ring-2 !ring-rose-400' : '' }}"
                />
                @if ($errors->has('restock_harga') && $isRestockTarget)
                    <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('restock_harga') }}</p>
                @endif
            </div>
        </div>
        <div class="bakery-field">
            <label for="field-restock-catatan-{{ $m->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">
                {{ __('stock.field_note') }}
            </label>
            <textarea
                id="field-restock-catatan-{{ $m->id }}"
                name="restock_catatan"
                rows="2"
                placeholder="{{ __('stock.note_placeholder') }}"
                class="bakery-input w-full {{ $errors->has('restock_catatan') && $isRestockTarget ? '!ring-2 !ring-rose-400' : '' }}"
            >{{ $restockCatatan }}</textarea>
            @if ($errors->has('restock_catatan') && $isRestockTarget)
                <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('restock_catatan') }}</p>
            @endif
        </div>
        <x-form-actions />
    </form>
</x-modal>
