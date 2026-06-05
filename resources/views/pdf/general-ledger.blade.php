@extends('pdf.layout')

@php
    use App\Support\FormatHelper;
    $positionLabel = $account->posisi === 'Credit' ? __('reports.position_cr') : __('reports.position_dr');
@endphp

@section('pdf-body')
<p class="report-subtitle" style="margin-bottom:12px;">
    <strong>{{ $account->kode }}</strong> — {{ $account->nama }}
    · {{ $account->grup }} · {{ __('reports.general_ledger.position_label') }} {{ $positionLabel }}
</p>

<table class="summary-grid">
    <tr>
        <td>
            <div class="summary-label">{{ __('reports.general_ledger.summary_opening') }}</div>
            <div class="summary-value">{{ FormatHelper::glBalance($opening_balance) }}</div>
        </td>
        <td>
            <div class="summary-label">{{ __('reports.general_ledger.movement') }}</div>
            <div class="summary-value">{{ __('reports.general_ledger.rows_count', ['count' => $rows->where(fn ($r) => ! ($r['is_opening'] ?? false))->count()]) }}</div>
        </td>
        <td class="highlight">
            <div class="summary-label">{{ __('reports.general_ledger.summary_closing') }}</div>
            <div class="summary-value">{{ FormatHelper::glBalance($closing_balance) }}</div>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th class="center" style="width:32px">{{ __('reports.general_ledger.col_no') }}</th>
            <th style="width:72px">{{ __('app.common.date') }}</th>
            <th>{{ __('reports.general_ledger.col_ref') }}</th>
            <th class="num" style="width:96px">{{ __('reports.debit') }}</th>
            <th class="num" style="width:96px">{{ __('reports.credit') }}</th>
            <th class="num" style="width:104px">{{ __('reports.balance') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $r)
            <tr class="{{ ($r['is_opening'] ?? false) ? 'opening' : '' }}">
                <td class="center">{{ $r['no'] !== '' ? $r['no'] : '—' }}</td>
                <td>{{ FormatHelper::dateGl($r['tgl']) }}</td>
                <td>{{ $r['ref'] }}</td>
                <td class="num">{{ FormatHelper::glAmount($r['debit']) }}</td>
                <td class="num">{{ FormatHelper::glAmount($r['kredit']) }}</td>
                <td class="num"><strong>{{ FormatHelper::glBalance($r['saldo']) }}</strong></td>
            </tr>
        @endforeach
        <tr class="total">
            <td colspan="3">{{ __('reports.general_ledger.closing_balance') }}</td>
            <td colspan="2"></td>
            <td class="num">{{ FormatHelper::glBalance($closing_balance) }}</td>
        </tr>
    </tbody>
</table>
@endsection
