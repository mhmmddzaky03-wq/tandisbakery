@php
    use App\Support\FormatHelper;

    $filterUrl = static function (?string $jenis): string {
        $query = request()->except('jenis');
        if ($jenis !== null && $jenis !== '') {
            $query['jenis'] = $jenis;
        }

        return request()->url().($query ? '?'.http_build_query($query) : '');
    };

    $jenisLabel = static fn (string $jenis): string => $jenis === 'Fixed'
        ? __('page.jenis_fixed')
        : __('page.jenis_variable');
@endphp
<div>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($stats as $s)
            @php $toneMap = ['violet' => 'violet', 'blue' => 'blue', 'green' => 'blue', 'amber' => 'amber', 'rose' => 'rose', 'slate' => 'amber']; @endphp
            <x-kpi-card :title="$s['label']" :value="$s['value']" :tone="$toneMap[$s['tone']] ?? 'amber'" :icon="$s['icon'] ?? null" />
        @endforeach
    </div>

    <div class="mt-6 bakery-card" data-table-search>
        <div class="bakery-card-header flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div class="text-lg font-extrabold text-slate-900">{{ __('page.cost_list_title') }}</div>
            <x-table-search
                :placeholder="__('page.search_cost')"
                :value="''"
            />
        </div>

        <div class="bakery-card-body bakery-table-wrap pt-2">
            <table class="bakery-table">
                <thead>
                    <tr>
                        <th class="w-[90px]">{{ __('page.id') }}</th>
                        <th class="w-[120px]">{{ __('page.date') }}</th>
                        <th class="w-[120px]">{{ __('page.category') }}</th>
                        <th class="w-[150px]">{{ __('page.description') }}</th>
                        <th class="w-[140px]">{{ __('page.amount') }}</th>
                        <th class="w-[130px]">
                            <div class="relative inline-flex items-center gap-1.5" data-dropdown>
                                <span>{{ __('page.cost_type') }}</span>
                                <button
                                    type="button"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-amber-600 {{ ! empty($filter) ? 'bg-amber-50 text-amber-600' : '' }}"
                                    data-dropdown-button
                                    aria-label="{{ __('page.filter_by_type') }}"
                                    title="{{ __('page.filter_by_type') }}"
                                >
                                    <x-icons.filter class="h-3.5 w-3.5" />
                                </button>
                                <div
                                    class="absolute left-0 top-full z-50 mt-2 hidden min-w-[148px] rounded-xl bg-white p-1.5 shadow-lg ring-1 ring-black/10"
                                    data-dropdown-menu
                                >
                                    <a
                                        href="{{ $filterUrl(null) }}"
                                        class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 {{ empty($filter) ? 'bg-amber-50 text-amber-800' : '' }}"
                                    >
                                        {{ __('page.all') }}
                                    </a>
                                    <a
                                        href="{{ $filterUrl('Fixed') }}"
                                        class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 {{ ($filter ?? '') === 'Fixed' ? 'bg-amber-50 text-amber-800' : '' }}"
                                    >
                                        {{ __('page.jenis_fixed') }}
                                    </a>
                                    <a
                                        href="{{ $filterUrl('Variable') }}"
                                        class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 {{ ($filter ?? '') === 'Variable' ? 'bg-amber-50 text-amber-800' : '' }}"
                                    >
                                        {{ __('page.jenis_variable') }}
                                    </a>
                                </div>
                            </div>
                        </th>
                        @if ($canEdit ?? true)
                            <th class="w-[90px] text-center">{{ __('page.action') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody data-table-search-body>
                    @forelse ($costs as $c)
                        <tr
                            data-searchable-row
                            data-search="{{ strtolower($c->id.' '.$c->kat.' '.$c->desk.' '.$c->jenis.' '.$jenisLabel($c->jenis)) }}"
                        >
                            <td class="font-bold text-slate-800">{{ $c->id }}</td>
                            <td>{{ FormatHelper::dateId($c->tanggal) }}</td>
                            <td>{{ $c->kat }}</td>
                            <td>
                                <button
                                    type="button"
                                    class="inline-flex w-[9.5rem] cursor-pointer items-center justify-between gap-2 rounded-full border-0 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                                    data-modal-open="detail-cost-{{ $c->id }}"
                                    title="{{ __('page.view_detail') }}"
                                    aria-label="{{ __('page.view_detail') }}"
                                >
                                    <span class="truncate">{{ \Illuminate\Support\Str::limit($c->desk, 22) }}</span>
                                    <x-icons.info-circle class="h-3.5 w-3.5 shrink-0 opacity-80" />
                                </button>
                            </td>
                            <td class="font-extrabold text-rose-600">{{ FormatHelper::rupiah($c->jumlah) }}</td>
                            <td>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $c->jenis === 'Fixed' ? 'bg-sky-50 text-sky-600' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $jenisLabel($c->jenis) }}
                                </span>
                            </td>
                            @if ($canEdit ?? true)
                                <td>
                                    <div class="flex items-center justify-center gap-1">
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                                            data-modal-open="edit-cost-{{ $c->id }}"
                                            title="{{ __('ui.edit') }}"
                                            aria-label="{{ __('ui.edit') }}"
                                        >
                                            <x-icons.pencil />
                                        </button>
                                        <form id="delete-cost-{{ $c->id }}" method="POST" action="{{ route($destroyRoute, $c->id) }}" class="inline">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                            onclick="if (window.confirm(@js(__('ui.confirm_delete_cost')))) document.getElementById('delete-cost-{{ $c->id }}').submit()"
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
                            <td colspan="{{ ($canEdit ?? true) ? 7 : 6 }}" class="px-4 py-12 text-center text-sm text-slate-500">
                                {{ __('page.cost_empty') }}
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

    @foreach ($costs as $c)
        <x-modal id="detail-cost-{{ $c->id }}" size="sm" :title="__('page.cost_detail')" :subtitle="$c->id">
            <dl class="text-sm">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">{{ __('page.date') }}</dt>
                    <dd class="font-semibold text-slate-800">{{ FormatHelper::dateId($c->tanggal) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">{{ __('page.category') }}</dt>
                    <dd class="text-right font-semibold text-slate-800">{{ $c->kat }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">{{ __('page.amount') }}</dt>
                    <dd class="font-semibold text-rose-600">{{ FormatHelper::rupiah($c->jumlah) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                    <dt class="text-slate-400">{{ __('page.cost_type') }}</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-bold {{ $c->jenis === 'Fixed' ? 'bg-sky-50 text-sky-600' : 'bg-amber-50 text-amber-700' }}">
                            {{ $jenisLabel($c->jenis) }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-start justify-between gap-4 py-2.5">
                    <dt class="shrink-0 text-slate-400">{{ __('page.description') }}</dt>
                    <dd class="max-w-[60%] text-right text-slate-700">{{ $c->desk ?: '—' }}</dd>
                </div>
            </dl>
            <div class="mt-4 flex justify-end border-t border-slate-100 pt-3">
                <button type="button" class="bakery-btn-ghost text-sm" data-modal-close>{{ __('ui.close') }}</button>
            </div>
        </x-modal>
    @endforeach

    @if ($canEdit ?? true)
        @foreach ($costs as $c)
            <x-modal id="edit-cost-{{ $c->id }}" :title="__('page.edit_cost')" :subtitle="$c->id">
                <form method="POST" action="{{ route($updateRoute, $c->id) }}" class="space-y-4" data-modal-form>
                    @csrf @method('PUT')
                    <x-form-field :label="__('page.id')" name="id_display" type="text" :value="$c->id" disabled />
                    <x-form-field :label="__('page.date')" name="tanggal" type="date" :value="old('tanggal', $c->tanggal->format('Y-m-d'))" required autofocus />
                    <x-form-field :label="__('page.category')" name="kat" :value="old('kat', $c->kat)" required :helper="__('page.cost_category_helper')" />
                    <x-form-field :label="__('page.description')" name="desk" type="textarea" :value="old('desk', $c->desk)" required />
                    <x-form-field :label="__('page.amount_rp')" name="jumlah" type="number" :value="old('jumlah', $c->jumlah)" min="0" required />
                    <x-form-field :label="__('page.cost_type')" name="jenis" type="select" required>
                        <option value="Variable" @selected(old('jenis', $c->jenis) === 'Variable')>{{ __('page.jenis_variable') }}</option>
                        <option value="Fixed" @selected(old('jenis', $c->jenis) === 'Fixed')>{{ __('page.jenis_fixed') }}</option>
                    </x-form-field>
                    <x-form-actions />
                </form>
            </x-modal>
        @endforeach
    @endif

    @if ($canAdd ?? true)
        <x-modal id="cost-baru" :title="__('page.add_cost_modal')" :subtitle="__('page.cost_list_subtitle')" :auto-open="$errors->has('tanggal') || $errors->has('kat')">
            <form method="POST" action="{{ route($storeRoute) }}" class="space-y-4" data-modal-form>
                @csrf
                <x-form-field :label="__('page.date')" name="tanggal" type="date" :value="old('tanggal', date('Y-m-d'))" required autofocus />
                <x-form-field :label="__('page.category')" name="kat" :value="old('kat')" required :placeholder="__('page.cost_category_placeholder')" />
                <x-form-field :label="__('page.description')" name="desk" type="textarea" :value="old('desk')" required :placeholder="__('page.cost_description_placeholder')" />
                <x-form-field :label="__('page.amount_rp')" name="jumlah" type="number" :value="old('jumlah')" min="0" required />
                <x-form-field :label="__('page.cost_type')" name="jenis" type="select" required>
                    <option value="Variable" @selected(old('jenis', 'Variable') === 'Variable')>{{ __('page.jenis_variable') }}</option>
                    <option value="Fixed" @selected(old('jenis') === 'Fixed')">{{ __('page.jenis_fixed') }}</option>
                </x-form-field>
                <x-form-actions />
            </form>
        </x-modal>
    @endif
</div>
