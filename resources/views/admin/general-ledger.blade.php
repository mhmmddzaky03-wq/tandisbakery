@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.gl';
    $pageTitle = __('app.pages.general_ledger');
    $pageSubtitle = $account->kode.' — '.$account->nama.' · '.FormatHelper::dateId($from).' s/d '.FormatHelper::dateId($to);
    $positionLabel = $account->posisi === 'Credit' ? 'Cr' : 'Dr';
    $txCount = $rows->where(fn ($r) => ! ($r['is_opening'] ?? false))->count();
    $periodDebit = $rows->where(fn ($r) => ! ($r['is_opening'] ?? false))->sum('debit');
    $periodKredit = $rows->where(fn ($r) => ! ($r['is_opening'] ?? false))->sum('kredit');
@endphp

@push('page-actions')
    <x-pdf-print-button
        :route="route('admin.pdf.gl')"
        :query="array_filter(['account' => $accountKode, 'from' => $from, 'to' => $to], fn ($v) => $v !== null && $v !== '')"
    />
@endpush

@section('content')
<div class="space-y-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="min-w-[min(100%,280px)] flex-1 sm:max-w-md">
            <label for="account" class="mb-1 block text-xs font-bold text-slate-600">{{ __('reports.account') }}</label>
            <select id="account" name="account" class="bakery-input w-full">
                @foreach ($accounts as $a)
                    <option value="{{ $a->kode }}" @selected($accountKode === $a->kode)>
                        {{ $a->kode }} — {{ $a->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="from" class="mb-1 block text-xs font-bold text-slate-600">{{ __('app.common.from') }}</label>
            <input type="date" id="from" name="from" value="{{ $from }}" class="bakery-input" />
        </div>
        <div>
            <label for="to" class="mb-1 block text-xs font-bold text-slate-600">{{ __('app.common.to') }}</label>
            <input type="date" id="to" name="to" value="{{ $to }}" class="bakery-input" />
        </div>
        <button type="submit" class="bakery-btn-primary shrink-0">{{ __('reports.show') }}</button>
    </form>

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-4 ring-1 ring-black/5">
            <p class="text-xs font-semibold text-slate-500">{{ __('reports.general_ledger.opening_balance') }}</p>
            <p class="mt-1 text-lg font-extrabold tabular-nums text-slate-800">{{ FormatHelper::glBalance($opening_balance) }}</p>
            <p class="mt-0.5 text-xs text-slate-400">{{ __('reports.general_ledger.as_of', ['date' => FormatHelper::dateId($from)]) }}</p>
        </div>
        <div class="rounded-2xl bg-white p-4 ring-1 ring-black/5">
            <p class="text-xs font-semibold text-slate-500">{{ __('reports.general_ledger.period_movement') }}</p>
            <p class="mt-1 text-lg font-extrabold text-slate-800">{{ __('reports.general_ledger.transactions_count', ['count' => $txCount]) }}</p>
            <p class="mt-0.5 text-xs text-slate-400">
                D {{ $periodDebit > 0 ? FormatHelper::rupiah($periodDebit) : '—' }}
                · K {{ $periodKredit > 0 ? FormatHelper::rupiah($periodKredit) : '—' }}
            </p>
        </div>
        <div class="rounded-2xl bg-amber-50 p-4 ring-1 ring-amber-200/60">
            <p class="text-xs font-semibold text-amber-800/80">{{ __('reports.general_ledger.closing_balance') }}</p>
            <p class="mt-1 text-lg font-extrabold tabular-nums text-amber-900">{{ FormatHelper::glBalance($closing_balance) }}</p>
            <p class="mt-0.5 text-xs text-amber-700/70">{{ __('reports.general_ledger.as_of', ['date' => FormatHelper::dateId($to)]) }}</p>
        </div>
    </div>

    <div class="bakery-card overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50 px-4 py-3 sm:px-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ __('reports.general_ledger.title') }}</p>
                    <p class="mt-0.5 text-base font-extrabold text-slate-900">
                        <span class="text-amber-700">{{ $account->kode }}</span>
                        {{ $account->nama }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ $account->grup }} · {{ $account->sub_grup }}
                        · {{ __('reports.general_ledger.normal_position') }}
                        <span class="bakery-badge ml-1 bg-white text-slate-700">{{ $positionLabel }}</span>
                    </p>
                    <p class="mt-2 text-xs text-slate-500">
                        {{ __('reports.general_ledger.show_journal_desc', ['from' => FormatHelper::dateId($from), 'to' => FormatHelper::dateId($to)]) }}
                    </p>
                </div>
                <div class="shrink-0 text-right text-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ __('reports.general_ledger.period_label') }}</p>
                    <p class="font-semibold text-slate-800">{{ FormatHelper::dateGl($from) }} – {{ FormatHelper::dateGl($to) }}</p>
                </div>
            </div>
        </div>

        <div class="bakery-card-body bakery-table-wrap">
            <table class="bakery-table text-sm">
                <thead>
                    <tr>
                        <th class="w-12 text-center">{{ __('reports.general_ledger.col_no') }}</th>
                        <th class="w-[96px]">{{ __('app.common.date') }}</th>
                        <th class="min-w-[140px]">{{ __('reports.general_ledger.col_ref') }}</th>
                        <th class="w-[128px] text-right">{{ __('reports.debit') }}</th>
                        <th class="w-[128px] text-right">{{ __('reports.credit') }}</th>
                        <th class="w-[148px] text-right">{{ __('reports.balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $r)
                        @php
                            $isOpening = $r['is_opening'] ?? false;
                            $saldoNegatif = $r['saldo'] < 0;
                        @endphp
                        <tr class="{{ $isOpening ? 'bg-amber-50/90 font-semibold text-slate-800' : 'text-slate-800' }}">
                            <td class="text-center text-slate-500">
                                {{ $r['no'] !== '' ? $r['no'] : '—' }}
                            </td>
                            <td class="whitespace-nowrap">{{ FormatHelper::dateGl($r['tgl']) }}</td>
                            <td>
                                @if ($isOpening)
                                    <span class="text-amber-900">{{ $r['ref'] }}</span>
                                @else
                                    {{ $r['ref'] }}
                                @endif
                            </td>
                            <td class="text-right tabular-nums {{ $r['debit'] > 0 ? 'font-medium' : 'text-slate-400' }}">
                                {{ FormatHelper::glAmount($r['debit']) }}
                            </td>
                            <td class="text-right tabular-nums {{ $r['kredit'] > 0 ? 'font-medium' : 'text-slate-400' }}">
                                {{ FormatHelper::glAmount($r['kredit']) }}
                            </td>
                            <td class="text-right tabular-nums font-semibold {{ $saldoNegatif ? 'text-rose-600' : ($isOpening ? 'text-slate-700' : 'text-slate-900') }}">
                                {{ FormatHelper::glBalance($r['saldo']) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="!py-14 text-center">
                                <p class="font-semibold text-slate-600">{{ __('reports.not_found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                    @if ($rows->isNotEmpty())
                        <tr class="bg-amber-50 font-extrabold text-slate-900">
                            <td colspan="3" class="text-right sm:text-left">{{ __('reports.general_ledger.closing_row') }}</td>
                            <td class="text-right tabular-nums text-slate-600">
                                {{ $periodDebit > 0 ? FormatHelper::rupiah($periodDebit) : '—' }}
                            </td>
                            <td class="text-right tabular-nums text-slate-600">
                                {{ $periodKredit > 0 ? FormatHelper::rupiah($periodKredit) : '—' }}
                            </td>
                            <td class="text-right tabular-nums text-amber-900">
                                {{ FormatHelper::glBalance($closing_balance) }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
