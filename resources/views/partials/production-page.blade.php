@php
    use App\Support\FormatHelper;
    use App\Support\LocaleLabels;

    $showRoute = $showRoute ?? 'admin.produksi.show';
@endphp
<div>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $s)
            @php $toneMap = ['blue' => 'blue', 'green' => 'green', 'rose' => 'rose', 'amber' => 'amber', 'slate' => 'amber']; @endphp
            <x-kpi-card :title="$s['label']" :value="$s['value']" :tone="$toneMap[$s['tone']] ?? 'amber'" :icon="$s['icon'] ?? null" />
        @endforeach
    </div>

    <div class="mt-5 bakery-card" data-table-search>
        <div class="bakery-card-header bakery-card-header--bordered">
            <div class="bakery-card-header__title">{{ __('app.tables.production_list') }}</div>
            <div class="bakery-card-header__actions">
            <x-table-search
                :placeholder="__('production.search')"
                :value="$search ?? ''"
            />
            </div>
        </div>

        <div class="bakery-card-body bakery-table-wrap pt-4">
            <table class="bakery-table">
                <thead>
                    <tr>
                        <th class="w-[90px]">{{ __('app.common.id') }}</th>
                        <th class="w-[120px]">{{ __('app.common.date') }}</th>
                        <th>{{ __('production.table_product_name') }}</th>
                        <th class="w-[130px]">{{ __('app.common.quantity') }}</th>
                        <th class="w-[110px] text-center">{{ __('app.common.status') }}</th>
                        <th class="w-[100px] text-center">{{ __('app.common.action') }}</th>
                    </tr>
                </thead>
                <tbody data-table-search-body>
                    @forelse ($records as $r)
                        <tr
                            data-searchable-row
                            data-search="{{ strtolower($r->id.' '.$r->product_name.' '.$r->status.' '.($r->product?->id ?? '').' '.($r->notes ?? '')) }}"
                        >
                            <td class="font-bold text-slate-800">
                                <a href="{{ route($showRoute, $r->id) }}" class="hover:text-sky-600">{{ $r->id }}</a>
                            </td>
                            <td>{{ FormatHelper::dateId($r->tanggal) }}</td>
                            <td>
                                <a href="{{ route($showRoute, $r->id) }}" class="font-semibold text-slate-800 hover:text-sky-600">{{ $r->product_name }}</a>
                            </td>
                            <td>{{ number_format($r->jumlah, 0, ',', '.') }} {{ $r->satuan }}</td>
                            <td class="text-center">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $r->status === 'Berhasil' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                    {{ LocaleLabels::productionStatus($r->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a
                                        href="{{ route($showRoute, $r->id) }}"
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
                                            data-modal-open="edit-prod-{{ $r->id }}"
                                            title="{{ __('app.common.edit') }}"
                                            aria-label="{{ __('app.common.edit') }}"
                                        >
                                            <x-icons.pencil />
                                        </button>
                                        <form id="delete-prod-{{ $r->id }}" method="POST" action="{{ route($destroyRoute, $r->id) }}" class="inline">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                            data-delete-form="delete-prod-{{ $r->id }}"
                                            data-confirm-message="{{ __('production.confirm_delete') }}"
                                            onclick="handleConfirmDelete(this)"
                                            title="{{ __('app.common.delete') }}"
                                            aria-label="{{ __('app.common.delete') }}"
                                        >
                                            <x-icons.trash />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr data-table-empty>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">
                                {{ __('production.empty') }}
                            </td>
                        </tr>
                    @endforelse
                    <tr data-table-no-results class="hidden">
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">
                            {{ __('app.common.not_found') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if ($canEdit ?? true)
        @foreach ($records as $r)
            @include('partials.production-edit-modal', ['r' => $r])
        @endforeach
    @endif

    @if ($canAdd ?? true)
        <x-modal
            id="prod-baru"
            size="lg"
            :title="__('production.modal_add')"
            :subtitle="__('production.modal_add_sub')"
            :scrollable="true"
            :auto-open="! old('_edit_id') && ($errors->has('tanggal') || $errors->has('product_name') || $errors->has('jumlah') || $errors->has('status') || $errors->has('notes') || $errors->has('use_bahan_dasar') || $errors->has('materials') || $errors->has('materials.*') || $errors->has('bahan_dasar') || $errors->has('bahan_dasar.*'))"
        >
            <form id="form-prod-baru" method="POST" action="{{ route($storeRoute) }}" data-modal-form data-production-form>
                @csrf
                @include('partials.production-form-body', [
                    'prefix' => 'create',
                    'catalogProductNames' => $catalogProductNames ?? collect(),
                    'tanggal' => date('Y-m-d'),
                    'productName' => '',
                    'jumlah' => '',
                    'satuan' => '',
                    'status' => 'Berhasil',
                    'notes' => '',
                    'materials' => $materials,
                    'materialRows' => old('materials', old('use_bahan_dasar') == '1' ? [] : [['raw_material_id' => '', 'raw_material_restock_id' => '', 'jumlah' => '', 'satuan' => '']]),
                    'bahanDasarItems' => $bahanDasarItems ?? collect(),
                    'bahanDasarRows' => old('bahan_dasar', []),
                    'autofocus' => true,
                ])
            </form>
            <x-slot:footer>
                <x-form-actions form="form-prod-baru" compact />
            </x-slot:footer>
        </x-modal>
    @endif
</div>
