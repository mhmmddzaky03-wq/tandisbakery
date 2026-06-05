@extends('pdf.layout')

@php use App\Support\FormatHelper; @endphp

@section('pdf-body')
<table class="summary-grid">
    <tr>
        <td>
            <div class="summary-label">{{ __('reports.total_debit') }}</div>
            <div class="summary-value">{{ FormatHelper::rupiah($totalDebit) }}</div>
        </td>
        <td>
            <div class="summary-label">{{ __('reports.total_credit') }}</div>
            <div class="summary-value">{{ FormatHelper::rupiah($totalKredit) }}</div>
        </td>
        <td class="highlight">
            <div class="summary-label">{{ __('reports.trial_balance.difference') }}</div>
            <div class="summary-value">{{ FormatHelper::rupiahTb($difference) }}</div>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width:52px">{{ __('reports.trial_balance.col_code') }}</th>
            <th>{{ __('reports.account') }}</th>
            <th class="center" style="width:28px">{{ __('reports.pos') }}</th>
            <th style="width:70px">{{ __('reports.trial_balance.col_group') }}</th>
            <th class="num" style="width:88px">{{ __('reports.debit') }}</th>
            <th class="num" style="width:88px">{{ __('reports.credit') }}</th>
            <th class="num" style="width:88px">{{ __('reports.trial_balance.col_subtotal') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            @php
                $acc = $row['account'];
                $pos = $acc->posisi === 'Credit' ? __('reports.position_cr') : __('reports.position_dr');
                $empty = $row['debit'] === 0 && $row['kredit'] === 0;
            @endphp
            <tr class="{{ $empty ? 'muted' : '' }}">
                <td><strong>{{ $acc->kode }}</strong></td>
                <td>{{ $acc->nama }}</td>
                <td class="center">{{ $pos }}</td>
                <td>{{ $acc->grup }}</td>
                <td class="num">{{ $row['debit'] > 0 ? FormatHelper::rupiahTb($row['debit']) : '-' }}</td>
                <td class="num">{{ $row['kredit'] > 0 ? FormatHelper::rupiahTb($row['kredit']) : '-' }}</td>
                <td class="num">{{ FormatHelper::rupiahTb($row['sub_total']) }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td colspan="4">{{ __('reports.trial_balance.total') }}</td>
            <td class="num">{{ FormatHelper::rupiahTb($totalDebit) }}</td>
            <td class="num">{{ FormatHelper::rupiahTb($totalKredit) }}</td>
            <td class="num">{{ FormatHelper::rupiahTb($difference) }}</td>
        </tr>
    </tbody>
</table>
@endsection
