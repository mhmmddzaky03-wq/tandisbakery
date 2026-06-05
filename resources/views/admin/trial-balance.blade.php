@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.tb';
    $pageTitle = __('app.pages.trial_balance');
    $pageSubtitle = __('app.pages.trial_balance_subtitle', ['date' => FormatHelper::dateId($asOf)]);
@endphp

@push('page-actions')
    <x-pdf-print-button :route="route('admin.pdf.tb')" :query="['as_of' => $asOf]" />
@endpush

@section('content')
<div class="space-y-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label for="as_of" class="mb-1 block text-xs font-bold text-slate-600">{{ __('reports.as_of') }}</label>
            <input type="date" id="as_of" name="as_of" value="{{ $asOf }}" class="bakery-input" />
        </div>
        <button type="submit" class="bakery-btn-primary shrink-0">{{ __('reports.show') }}</button>
    </form>

    <div class="bakery-card overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50 px-4 py-3 sm:px-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ __('reports.trial_balance.header') }}</p>
                    <p class="text-sm font-semibold text-slate-700">{{ __('reports.trial_balance.per_date', ['date' => FormatHelper::dateId($asOf)]) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ __('reports.trial_balance.source_note') }}</p>
                </div>
                <div class="text-right text-sm">
                    <p class="font-bold text-slate-800">
                        {{ __('reports.trial_balance.total') }}
                        <span class="ml-2 text-amber-700">{{ FormatHelper::rupiah($totalDebit) }}</span>
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ __('reports.trial_balance.difference') }}:
                        <span class="{{ $difference === 0 ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                            {{ FormatHelper::rupiahTb($difference) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="bakery-card-body bakery-table-wrap">
            <table class="bakery-table text-sm">
                <thead>
                    <tr>
                        <th class="w-[72px]">{{ __('reports.trial_balance.col_code') }}</th>
                        <th class="min-w-[200px]">{{ __('reports.account') }}</th>
                        <th class="w-[44px] text-center">{{ __('reports.pos') }}</th>
                        <th class="w-[88px]">{{ __('reports.trial_balance.col_group') }}</th>
                        <th class="w-[140px]">{{ __('reports.trial_balance.col_subgroup') }}</th>
                        <th class="w-[120px] text-right">{{ __('reports.debit') }}</th>
                        <th class="w-[120px] text-right">{{ __('reports.credit') }}</th>
                        <th class="w-[120px] text-right">{{ __('reports.trial_balance.col_subtotal') }}</th>
                        <th class="w-[120px] text-right">{{ __('reports.trial_balance.col_for_fs') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        @php
                            $acc = $row['account'];
                            $pos = $acc->posisi === 'Credit' ? 'Cr' : 'Dr';
                        @endphp
                        @php
                            $isEmpty = $row['debit'] === 0 && $row['kredit'] === 0;
                        @endphp
                        <tr class="{{ $isEmpty ? 'text-slate-400' : 'text-slate-800' }}">
                            <td class="font-bold">{{ $acc->kode }}</td>
                            <td>{{ $acc->nama }}</td>
                            <td class="text-center font-semibold">{{ $pos }}</td>
                            <td>{{ $acc->grup }}</td>
                            <td>{{ $acc->sub_grup }}</td>
                            <td class="text-right font-medium tabular-nums">
                                {{ $row['debit'] > 0 ? FormatHelper::rupiahTb($row['debit']) : '-' }}
                            </td>
                            <td class="text-right font-medium tabular-nums">
                                {{ $row['kredit'] > 0 ? FormatHelper::rupiahTb($row['kredit']) : '-' }}
                            </td>
                            <td class="text-right tabular-nums">{{ FormatHelper::rupiahTb($row['sub_total']) }}</td>
                            <td class="text-right tabular-nums">{{ FormatHelper::rupiahTb($row['for_fs']) }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-amber-50 font-extrabold text-slate-900">
                        <td colspan="5">{{ __('reports.trial_balance.total') }}</td>
                        <td class="text-right tabular-nums">{{ FormatHelper::rupiahTb($totalDebit) }}</td>
                        <td class="text-right tabular-nums">{{ FormatHelper::rupiahTb($totalKredit) }}</td>
                        <td class="text-right tabular-nums">{{ FormatHelper::rupiahTb($difference) }}</td>
                        <td class="text-right tabular-nums">-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
