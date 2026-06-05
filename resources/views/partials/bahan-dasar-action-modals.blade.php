@php
    use App\Support\FormatHelper;

    $updateRoute = $updateRoute ?? 'admin.bahan_dasar.update';
    $buatAdonanRoute = $buatAdonanRoute ?? 'admin.bahan_dasar.buat_adonan';

    $editId = old('_edit_id');
    $hasMasterErrors = $errors->has('nama') || $errors->has('satuan') || $errors->has('min');
    $isEditTarget = $editId === $item->id && $hasMasterErrors;

    $materialRows = old('materials');
    if ($materialRows === null) {
        $materialRows = [['raw_material_id' => '', 'raw_material_restock_id' => '', 'jumlah' => '', 'satuan' => '']];
    }

    $hasBuatAdonanErrors = $errors->has('tanggal')
        || $errors->has('jumlah_hasil')
        || $errors->has('catatan')
        || $errors->has('materials')
        || $errors->has('materials.*');
    $isBuatAdonanTarget = old('_buat_adonan_id') === $item->id && $hasBuatAdonanErrors;
@endphp

<x-modal id="edit-bahan-dasar-{{ $item->id }}" :title="__('bahan_dasar.modal_edit')" :subtitle="$item->id" size="md" :auto-open="$isEditTarget">
    <form method="POST" action="{{ route($updateRoute, $item->id) }}" class="space-y-3" data-modal-form>
        @csrf @method('PUT')
        <input type="hidden" name="_edit_id" value="{{ $item->id }}" />
        <div>
            <label for="field-nama-bd-show-{{ $item->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('bahan_dasar.field_name') }} <span class="text-rose-500">*</span></label>
            <input id="field-nama-bd-show-{{ $item->id }}" name="nama" type="text" value="{{ old('nama', $item->nama) }}" required class="bakery-input h-11 w-full" />
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="field-satuan-bd-show-{{ $item->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('app.common.unit') }} <span class="text-rose-500">*</span></label>
                <select id="field-satuan-bd-show-{{ $item->id }}" name="satuan" required class="bakery-input h-11 w-full" @disabled($item->batches_count > 0)>
                    <option value="g" @selected(old('satuan', $item->satuan) === 'g')>{{ __('bahan_dasar.unit_gram') }}</option>
                    <option value="kg" @selected(old('satuan', $item->satuan) === 'kg')>{{ __('bahan_dasar.unit_kg') }}</option>
                </select>
            </div>
            <div>
                <label for="field-min-bd-show-{{ $item->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('stock.field_min') }} <span class="text-rose-500">*</span></label>
                <input id="field-min-bd-show-{{ $item->id }}" name="min" type="text" value="{{ old('min', FormatHelper::formatQtyInput($item->min)) }}" required inputmode="decimal" data-decimal-one class="bakery-input h-11 w-full" />
            </div>
        </div>
        <x-form-actions compact :submit="__('app.common.save_changes')" />
    </form>
</x-modal>

<x-modal id="buat-adonan-{{ $item->id }}" :title="__('bahan_dasar.modal_make_dough')" :subtitle="$item->nama" size="lg" :scrollable="true" :auto-open="$isBuatAdonanTarget">
    <form id="form-buat-adonan-{{ $item->id }}" method="POST" action="{{ route($buatAdonanRoute, $item->id) }}" class="space-y-4" data-modal-form data-production-form>
        @csrf
        <input type="hidden" name="_buat_adonan_id" value="{{ $item->id }}" />

        <div class="rounded-lg bg-violet-50/80 px-3 py-2.5 ring-1 ring-violet-100">
            <div class="font-bold text-violet-900">{{ $item->nama }}</div>
            <div class="mt-0.5 text-xs font-semibold text-violet-700/80">{{ __('bahan_dasar.field_current_stock', ['qty' => FormatHelper::formatQtyOne($item->jumlah), 'unit' => $item->satuan]) }}</div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="field-tanggal-adonan-{{ $item->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('app.common.date') }} <span class="text-rose-500">*</span></label>
                <input id="field-tanggal-adonan-{{ $item->id }}" name="tanggal" type="date" value="{{ old('tanggal', date('Y-m-d')) }}" required class="bakery-input h-11 w-full {{ $errors->has('tanggal') && $isBuatAdonanTarget ? '!ring-2 !ring-rose-400' : '' }}" />
            </div>
            <div>
                <label for="field-jumlah-adonan-{{ $item->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('bahan_dasar.field_result_qty') }} <span class="text-rose-500">*</span></label>
                <div class="flex items-center gap-2">
                    <input id="field-jumlah-adonan-{{ $item->id }}" name="jumlah_hasil" type="text" value="{{ old('jumlah_hasil') }}" required inputmode="decimal" data-decimal-one class="bakery-input h-11 min-w-0 flex-1 {{ $errors->has('jumlah_hasil') && $isBuatAdonanTarget ? '!ring-2 !ring-rose-400' : '' }}" />
                    <span class="inline-flex h-11 shrink-0 items-center rounded-lg bg-slate-100 px-3 text-xs font-bold uppercase text-slate-600">{{ $item->satuan }}</span>
                </div>
                @if ($errors->has('jumlah_hasil') && $isBuatAdonanTarget)
                    <p class="mt-1.5 text-xs font-semibold text-rose-600">{{ $errors->first('jumlah_hasil') }}</p>
                @endif
            </div>
        </div>

        <div>
            <label for="field-catatan-adonan-{{ $item->id }}" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('app.common.note') }}</label>
            <input id="field-catatan-adonan-{{ $item->id }}" name="catatan" type="text" value="{{ old('catatan') }}" maxlength="500" placeholder="{{ __('bahan_dasar.note_placeholder') }}" class="bakery-input h-11 w-full" />
        </div>

        <div>
            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">{{ __('bahan_dasar.section_raw_from_stock') }}</p>
            @include('partials.production-material-section', [
                'materials' => $materials,
                'initialRows' => $materialRows,
            ])
        </div>
    </form>
    <x-slot:footer>
        <x-form-actions :form="'form-buat-adonan-'.$item->id" compact :submit="__('bahan_dasar.submit_save_dough')" />
    </x-slot:footer>
</x-modal>
