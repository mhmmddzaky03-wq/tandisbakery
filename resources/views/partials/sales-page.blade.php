@php
    use App\Support\FormatHelper;
    use App\Support\LocaleLabels;

    $metodeClass = static fn (string $metode): string => match ($metode) {
        'Cash' => 'bg-emerald-50 text-emerald-700',
        'Transfer' => 'bg-sky-50 text-sky-600',
        default => 'bg-amber-50 text-amber-700',
    };
@endphp
<div>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($stats as $s)
            @php $toneMap = ['green' => 'green', 'blue' => 'blue', 'amber' => 'amber', 'violet' => 'violet']; @endphp
            <x-kpi-card :title="$s['label']" :value="$s['value']" :tone="$toneMap[$s['tone']] ?? 'amber'" :icon="$s['icon'] ?? null" />
        @endforeach
    </div>

    <div class="mt-6 bakery-card" data-table-search>
        <div class="bakery-card-header bakery-card-header--bordered">
            <div class="bakery-card-header__title">{{ __('app.tables.sales_list') }}</div>
            <div class="bakery-card-header__actions">
            <x-table-search :placeholder="__('sales.search')" :value="''" />
            </div>
        </div>

        <div class="bakery-card-body bakery-table-wrap pt-2">
            <table class="bakery-table">
                <thead>
                    <tr>
                        <th class="w-[110px]">{{ __('sales.table_transaction') }}</th>
                        <th class="w-[120px]">{{ __('app.common.date') }}</th>
                        <th class="w-[150px]">{{ __('sales.table_total_sales') }}</th>
                        <th class="w-[120px]">{{ __('sales.table_payment_method') }}</th>
                        <th class="w-[100px]">{{ __('sales.table_transaction_count') }}</th>
                        @if ($canEdit ?? true)
                            <th class="w-[90px] text-center">{{ __('app.common.action') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody data-table-search-body>
                    @forelse ($transactions as $t)
                        <tr
                            data-searchable-row
                            data-search="{{ strtolower($t->id.' '.FormatHelper::dateId($t->tanggal).' '.$t->total.' '.$t->metode.' '.LocaleLabels::paymentMethod($t->metode).' '.$t->jumlah) }}"
                        >
                            <td class="font-bold text-slate-800">{{ $t->id }}</td>
                            <td>{{ FormatHelper::dateId($t->tanggal) }}</td>
                            <td class="font-extrabold text-emerald-600">{{ FormatHelper::rupiah($t->total) }}</td>
                            <td>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $metodeClass($t->metode) }}">
                                    {{ LocaleLabels::paymentMethod($t->metode) }}
                                </span>
                            </td>
                            <td class="font-semibold text-slate-700">{{ number_format($t->jumlah, 0, ',', '.') }}</td>
                            @if ($canEdit ?? true)
                                <td>
                                    <div class="flex items-center justify-center gap-1">
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                                            data-modal-open="edit-sales-{{ $t->id }}"
                                            title="{{ __('app.common.edit') }}"
                                            aria-label="{{ __('app.common.edit') }}"
                                        >
                                            <x-icons.pencil />
                                        </button>
                                        <form id="delete-sales-{{ $t->id }}" method="POST" action="{{ route($destroyRoute, $t->id) }}" class="inline">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-50 hover:text-rose-600"
                                            onclick="if (window.confirm(@js(__('sales.confirm_delete')))) document.getElementById('delete-sales-{{ $t->id }}').submit()"
                                            title="{{ __('app.common.delete') }}"
                                            aria-label="{{ __('app.common.delete') }}"
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
                                {{ __('sales.empty') }}
                            </td>
                        </tr>
                    @endforelse
                    <tr data-table-no-results class="hidden">
                        <td colspan="{{ ($canEdit ?? true) ? 6 : 5 }}" class="px-4 py-12 text-center text-sm text-slate-500">
                            {{ __('app.common.not_found') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if ($canEdit ?? true)
        @php
            $editSalesId = old('_edit_id');
            $hasSalesErrors = $errors->has('tanggal') || $errors->has('total') || $errors->has('metode') || $errors->has('jumlah');
        @endphp
        @foreach ($transactions as $t)
                <x-modal id="edit-sales-{{ $t->id }}" :title="__('sales.modal_edit')" :subtitle="$t->id" :auto-open="$editSalesId === $t->id && $hasSalesErrors">
                    <form method="POST" action="{{ route($updateRoute, $t->id) }}" class="space-y-4" data-modal-form>
                        @csrf @method('PUT')
                        <input type="hidden" name="_edit_id" value="{{ $t->id }}" />
                    <x-form-field :label="__('app.common.date')" name="tanggal" type="date" :value="old('tanggal', $t->tanggal->format('Y-m-d'))" required autofocus />
                    <x-form-field :label="__('sales.field_total')" name="total" type="number" :value="old('total', $t->total)" min="0" required :helper="__('sales.hint_total')" />
                    <x-form-field :label="__('sales.field_payment_method')" name="metode" type="select" required>
                        <option value="Cash" @selected(old('metode', $t->metode) === 'Cash')>{{ __('sales.payment.cash') }}</option>
                        <option value="Transfer" @selected(old('metode', $t->metode) === 'Transfer')>{{ __('sales.payment.transfer') }}</option>
                        <option value="Mix" @selected(old('metode', $t->metode) === 'Mix')>{{ __('sales.payment.mixed') }}</option>
                    </x-form-field>
                    <x-form-field :label="__('sales.field_transaction_count')" name="jumlah" type="number" :value="old('jumlah', $t->jumlah)" min="1" required :helper="__('sales.hint_count')" />
                    <x-form-actions />
                </form>
            </x-modal>
        @endforeach
    @endif

    @if ($canAdd ?? true)
        <x-modal
            id="sales-baru"
            :title="__('sales.modal_add')"
            :subtitle="__('sales.modal_add_sub')"
            :auto-open="! old('_edit_id') && ($errors->has('tanggal') || $errors->has('total') || $errors->has('metode') || $errors->has('jumlah'))"
        >
            <form method="POST" action="{{ route($storeRoute) }}" class="space-y-4" data-modal-form>
                @csrf
                <x-form-field :label="__('app.common.date')" name="tanggal" type="date" :value="old('tanggal', date('Y-m-d'))" required autofocus />
                <x-form-field :label="__('sales.field_total')" name="total" type="number" :value="old('total')" min="0" required :helper="__('sales.hint_total')" />
                <x-form-field :label="__('sales.field_payment_method')" name="metode" type="select" required>
                    <option value="Cash" @selected(old('metode') === 'Cash')>{{ __('sales.payment.cash') }}</option>
                    <option value="Transfer" @selected(old('metode') === 'Transfer')>{{ __('sales.payment.transfer') }}</option>
                    <option value="Mix" @selected(old('metode', 'Mix') === 'Mix')>{{ __('sales.payment.mixed') }}</option>
                </x-form-field>
                <x-form-field :label="__('sales.field_transaction_count')" name="jumlah" type="number" :value="old('jumlah', 1)" min="1" required :helper="__('sales.hint_count')" />
                <x-form-actions />
            </form>
        </x-modal>
    @endif
</div>
