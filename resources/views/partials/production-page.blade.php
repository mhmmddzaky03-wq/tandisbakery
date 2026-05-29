@php use App\Support\FormatHelper; @endphp
<div>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $s)
            @php $toneMap = ['blue' => 'blue', 'green' => 'green', 'rose' => 'rose', 'amber' => 'amber', 'slate' => 'amber']; @endphp
            <x-kpi-card :title="$s['label']" :value="$s['value']" :tone="$toneMap[$s['tone']] ?? 'amber'" :icon="$s['icon'] ?? null" />
        @endforeach
    </div>

    <div class="mt-5 bakery-card" data-table-search>
        <div class="bakery-card-header flex items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div class="min-w-0 shrink text-lg font-extrabold text-slate-900">{{ __('page.production_list_title') }}</div>
            <x-table-search
                :placeholder="__('page.search_production')"
                :value="$search ?? ''"
            />
        </div>

        <div class="bakery-card-body bakery-table-wrap pt-4">
            <table class="bakery-table">
                <thead>
                    <tr>
                        <th class="w-[90px]">{{ __('page.id') }}</th>
                        <th class="w-[120px]">{{ __('page.date') }}</th>
                        <th>{{ __('page.product_name') }}</th>
                        <th class="w-[130px]">{{ __('page.quantity') }}</th>
                        <th class="w-[140px]">{{ __('page.status') }}</th>
                        @if ($canEdit ?? true)
                            <th class="w-[90px] text-center">{{ __('page.action') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody data-table-search-body>
                    @forelse ($records as $r)
                        <tr
                            data-searchable-row
                            data-search="{{ strtolower($r->id.' '.$r->product_name.' '.$r->status.' '.($r->product?->id ?? '').' '.($r->notes ?? '')) }}"
                        >
                            <td class="font-bold text-slate-800">{{ $r->id }}</td>
                            <td>{{ FormatHelper::dateId($r->tanggal) }}</td>
                            <td>{{ $r->product_name }}</td>
                            <td>{{ number_format($r->jumlah, 0, ',', '.') }} {{ $r->satuan }}</td>
                            <td>
                                <button
                                    type="button"
                                    class="inline-flex w-[7rem] cursor-pointer items-center justify-between gap-2 rounded-full border-0 px-3 py-1.5 text-xs font-bold transition hover:opacity-90 {{ $r->status === 'Berhasil' ? 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' : 'bg-rose-50 text-rose-600 hover:bg-rose-100' }}"
                                    data-modal-open="detail-prod-{{ $r->id }}"
                                    title="{{ __('page.view_detail') }}"
                                    aria-label="{{ __('page.view_detail') }}: {{ $r->status }}"
                                >
                                    <span class="truncate">{{ $r->status }}</span>
                                    <x-icons.info-circle class="h-3.5 w-3.5 shrink-0 opacity-80" />
                                </button>
                            </td>
                            @if ($canEdit ?? true)
                                <td>
                                    <div class="flex items-center justify-center gap-1">
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                                            data-modal-open="edit-prod-{{ $r->id }}"
                                            title="{{ __('ui.edit') }}"
                                            aria-label="{{ __('ui.edit') }}"
                                        >
                                            <x-icons.pencil />
                                        </button>
                                        <form id="delete-prod-{{ $r->id }}" method="POST" action="{{ route($destroyRoute, $r->id) }}" class="inline">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                            data-production-delete
                                            data-has-product="{{ $r->product ? '1' : '0' }}"
                                            data-delete-form="delete-prod-{{ $r->id }}"
                                            data-linked-message="{{ __('page.cannot_delete_linked') }}"
                                            data-confirm-message="{{ __('ui.confirm_delete_production') }}"
                                            onclick="handleProductionDelete(this)"
                                            title="{{ __('ui.delete') }}"
                                            aria-label="{{ __('ui.delete') }}"
                                        >
                                            <x-icons.trash />
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr data-table-empty>
                            <td colspan="{{ ($canEdit ?? true) ? 6 : 5 }}" class="px-4 py-12 text-center text-sm text-slate-500">
                                {{ __('page.production_empty') }}
                            </td>
                        </tr>
                    @endforelse
                    <tr data-table-no-results class="hidden">
                        <td colspan="{{ ($canEdit ?? true) ? 6 : 5 }}" class="px-4 py-12 text-center text-sm text-slate-500">
                            {{ __('ui.no_search_results') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($records as $r)
        <x-modal id="detail-prod-{{ $r->id }}" size="sm" :title="__('page.production_detail')" :subtitle="$r->id">
            <dl class="text-sm">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">{{ __('page.date') }}</dt>
                    <dd class="font-semibold text-slate-800">{{ FormatHelper::dateId($r->tanggal) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">{{ __('page.product_name') }}</dt>
                    <dd class="text-right font-semibold text-slate-800">{{ $r->product_name }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">{{ __('page.quantity') }}</dt>
                    <dd class="font-semibold text-slate-800">{{ number_format($r->jumlah, 0, ',', '.') }} {{ $r->satuan }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">{{ __('page.status') }}</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-bold {{ $r->status === 'Berhasil' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">{{ $r->status }}</span>
                    </dd>
                </div>
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="shrink-0 text-slate-400">{{ __('page.product_registered') }}</dt>
                    <dd class="text-right font-semibold text-slate-800">
                        @if ($r->product)
                            {{ $r->product->id }} — {{ $r->product->nama }}
                        @else
                            <span class="text-slate-400">{{ __('page.not_registered') }}</span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-start justify-between gap-4 py-2.5">
                    <dt class="shrink-0 text-slate-400">{{ __('page.notes') }}</dt>
                    <dd class="max-w-[60%] text-right text-slate-700">{{ $r->notes ?: '—' }}</dd>
                </div>
            </dl>
            <div class="mt-4 flex justify-end border-t border-slate-100 pt-3">
                <button type="button" class="bakery-btn-ghost text-sm" data-modal-close>{{ __('ui.close') }}</button>
            </div>
        </x-modal>
    @endforeach

    @if ($canEdit ?? true)
        @foreach ($records as $r)
            <x-modal id="edit-prod-{{ $r->id }}" :title="__('page.edit_production')" :subtitle="$r->id">
                <form method="POST" action="{{ route($updateRoute, $r->id) }}" class="space-y-4" data-modal-form>
                    @csrf @method('PUT')
                    <x-form-field :label="__('page.id')" name="id_display" type="text" :value="$r->id" disabled />
                    <x-form-field :label="__('page.date')" name="tanggal" type="date" :value="old('tanggal', $r->tanggal->format('Y-m-d'))" required autofocus />
                    <x-form-field :label="__('page.product_name')" name="product_name" :value="old('product_name', $r->product_name)" required />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-form-field :label="__('page.quantity')" name="jumlah" type="number" :value="old('jumlah', $r->jumlah)" min="0" required />
                        <x-form-field :label="__('page.unit')" name="satuan" :value="old('satuan', $r->satuan)" required />
                    </div>
                    <x-form-field :label="__('page.status')" name="status" type="select" required>
                        <option value="Berhasil" @selected(old('status', $r->status) === 'Berhasil')>Berhasil</option>
                        <option value="Gagal" @selected(old('status', $r->status) === 'Gagal')>Gagal</option>
                    </x-form-field>
                    <x-form-field :label="__('page.notes')" name="notes" type="textarea" :value="old('notes', $r->notes)" :helper="__('page.notes_helper')" />
                    @if ($r->product)
                        <p class="rounded-xl bg-sky-50 px-4 py-3 text-xs font-semibold text-sky-700">{{ __('page.production_linked_product', ['id' => $r->product->id]) }}</p>
                    @endif
                    <x-form-actions />
                </form>
            </x-modal>
        @endforeach
    @endif

    @if ($canAdd ?? true)
        <x-modal id="prod-baru" :title="__('page.add_production')" :auto-open="$errors->has('tanggal') || $errors->has('product_name')">
            <form method="POST" action="{{ route($storeRoute) }}" class="space-y-4" data-modal-form>
                @csrf
                <x-form-field :label="__('page.date')" name="tanggal" type="date" :value="old('tanggal', date('Y-m-d'))" required autofocus />
                <x-form-field :label="__('page.product_name')" name="product_name" :value="old('product_name')" required placeholder="Contoh: Roti Tawar" />
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-form-field :label="__('page.quantity')" name="jumlah" type="number" :value="old('jumlah')" min="0" required />
                    <x-form-field :label="__('page.unit')" name="satuan" :value="old('satuan')" required placeholder="loyang, pcs, kg" />
                </div>
                <x-form-field :label="__('page.status')" name="status" type="select" required>
                    <option value="Berhasil" @selected(old('status', 'Berhasil') === 'Berhasil')>Berhasil</option>
                    <option value="Gagal" @selected(old('status') === 'Gagal')>Gagal</option>
                </x-form-field>
                <x-form-field :label="__('page.notes')" name="notes" type="textarea" :value="old('notes')" :helper="__('page.notes_helper')" />
                <x-form-actions />
            </form>
        </x-modal>
    @endif
</div>
