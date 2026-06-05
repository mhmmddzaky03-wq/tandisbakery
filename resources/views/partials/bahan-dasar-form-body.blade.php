@php
    $prefix = $prefix ?? 'create';
    $isCreateTarget = $isCreateTarget ?? false;
    $tanggalValue = old('tanggal', $tanggal ?? date('Y-m-d'));
    $jumlahHasilValue = old('jumlah_hasil', $jumlahHasil ?? '');
    $catatanValue = old('catatan', $catatan ?? '');
    $materialRows = $materialRows ?? [['raw_material_id' => '', 'raw_material_restock_id' => '', 'jumlah' => '', 'satuan' => '']];
    $showFormErrors = $errors->any() && $isCreateTarget;
@endphp

<div class="space-y-5">
    <section>
        <div class="mb-3 flex items-center gap-2.5">
            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-violet-100 text-xs font-extrabold text-violet-700">1</span>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">{{ __('bahan_dasar.section_info') }}</h3>
                <p class="text-xs text-slate-500">{{ __('bahan_dasar.section_info_sub') }}</p>
            </div>
        </div>
        <div class="space-y-4 rounded-xl bg-slate-50/70 p-4 ring-1 ring-slate-100">
            @if ($showFormErrors)
                <div class="rounded-xl bg-rose-50 px-4 py-3 ring-1 ring-rose-200" role="alert" data-form-error-banner>
                    <p class="text-sm font-extrabold text-rose-800">{{ __('bahan_dasar.save_error') }}</p>
                    <ul class="mt-1.5 space-y-0.5 text-xs font-semibold text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div>
                <label for="field-nama-bd-{{ $prefix }}" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('bahan_dasar.field_name') }} <span class="text-rose-500">*</span></label>
                <input id="field-nama-bd-{{ $prefix }}" name="nama" type="text" value="{{ old('nama') }}" required data-title-case placeholder="{{ __('bahan_dasar.name_placeholder') }}" class="bakery-input h-11 w-full {{ $errors->has('nama') && $isCreateTarget ? '!ring-2 !ring-rose-400' : '' }}" />
                @error('nama')
                    <p class="mt-1.5 text-xs font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="field-satuan-bd-{{ $prefix }}" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('app.common.unit') }} <span class="text-rose-500">*</span></label>
                    <select id="field-satuan-bd-{{ $prefix }}" name="satuan" required class="bakery-input h-11 w-full">
                        <option value="g" @selected(old('satuan', 'g') === 'g')>{{ __('bahan_dasar.unit_gram') }}</option>
                        <option value="kg" @selected(old('satuan') === 'kg')>{{ __('bahan_dasar.unit_kg') }}</option>
                    </select>
                </div>
                <div>
                    <label for="field-min-bd-{{ $prefix }}" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('stock.field_min') }} <span class="text-rose-500">*</span></label>
                    <input id="field-min-bd-{{ $prefix }}" name="min" type="text" value="{{ old('min') }}" required inputmode="decimal" data-decimal-one class="bakery-input h-11 w-full" />
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="mb-3 flex items-center gap-2.5">
            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-amber-100 text-xs font-extrabold text-amber-700">2</span>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">{{ __('bahan_dasar.section_first_batch') }}</h3>
                <p class="text-xs text-slate-500">{{ __('bahan_dasar.section_first_batch_sub') }}</p>
            </div>
        </div>
        <div class="grid gap-3 rounded-xl bg-slate-50/70 p-4 ring-1 ring-slate-100 sm:grid-cols-2">
            <div>
                <label for="field-tanggal-bd-{{ $prefix }}" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('app.common.date') }} <span class="text-rose-500">*</span></label>
                <input id="field-tanggal-bd-{{ $prefix }}" name="tanggal" type="date" value="{{ $tanggalValue }}" required class="bakery-input h-11 w-full {{ $errors->has('tanggal') && $isCreateTarget ? '!ring-2 !ring-rose-400' : '' }}" />
            </div>
            <div>
                <label for="field-jumlah-bd-{{ $prefix }}" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('bahan_dasar.field_result_qty') }} <span class="text-rose-500">*</span></label>
                <input id="field-jumlah-bd-{{ $prefix }}" name="jumlah_hasil" type="text" value="{{ $jumlahHasilValue }}" required inputmode="decimal" data-decimal-one class="bakery-input h-11 w-full {{ $errors->has('jumlah_hasil') && $isCreateTarget ? '!ring-2 !ring-rose-400' : '' }}" />
                @error('jumlah_hasil')
                    <p class="mt-1.5 text-xs font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="sm:col-span-2">
                <label for="field-catatan-bd-{{ $prefix }}" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('app.common.note') }}</label>
                <input id="field-catatan-bd-{{ $prefix }}" name="catatan" type="text" value="{{ $catatanValue }}" maxlength="500" placeholder="{{ __('bahan_dasar.note_placeholder') }}" class="bakery-input h-11 w-full" />
            </div>
        </div>
    </section>

    <section>
        <div class="mb-3 flex items-center gap-2.5">
            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-sky-100 text-xs font-extrabold text-sky-700">3</span>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">{{ __('bahan_dasar.section_raw_usage') }}</h3>
                <p class="text-xs text-slate-500">{{ __('bahan_dasar.section_raw_usage_sub') }}</p>
            </div>
        </div>
        @include('partials.production-material-section', [
            'materials' => $materials,
            'initialRows' => $materialRows,
        ])
    </section>
</div>
