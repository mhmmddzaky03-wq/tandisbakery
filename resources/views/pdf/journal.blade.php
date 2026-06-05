@extends('pdf.layout')

@php use App\Support\FormatHelper; @endphp

@section('pdf-body')
<table class="summary-grid">
    <tr>
        <td>
            <div class="summary-label">{{ __('reports.journal.total_transactions') }}</div>
            <div class="summary-value">{{ number_format($totals['transaksi'], 0, ',', '.') }}</div>
        </td>
        <td>
            <div class="summary-label">{{ __('reports.journal.total_debit_label') }}</div>
            <div class="summary-value">{{ FormatHelper::rupiah($totals['debit']) }}</div>
        </td>
        <td class="highlight">
            <div class="summary-label">{{ __('reports.journal.total_credit_label') }}</div>
            <div class="summary-value">{{ FormatHelper::rupiah($totals['kredit']) }}</div>
        </td>
    </tr>
</table>

@forelse ($journals as $day)
    <div class="day-block">
        <div class="day-head">{{ $day['hari'] }}, {{ $day['tanggal'] }}</div>
        @foreach ($day['transactions'] as $tx)
            @php
                $source = $tx['source'];
                $badgeClass = match ($source['tone']) {
                    'emerald' => 'badge-emerald',
                    'sky' => 'badge-sky',
                    'violet' => 'badge-violet',
                    'amber' => 'badge-amber',
                    default => 'badge',
                };
            @endphp
            <div class="tx-head">
                <span class="badge {{ $badgeClass }}">{{ $source['label'] }}</span>
                @if ($tx['ref'])
                    <strong style="margin-left:6px">{{ $tx['ref'] }}</strong>
                @endif
                — {{ $tx['deskripsi'] }}
            </div>
            <table class="data-table" style="margin-bottom:0;border-top:0">
                <thead>
                    <tr>
                        <th style="width:56px">{{ __('reports.trial_balance.col_code') }}</th>
                        <th>{{ __('app.common.name') }}</th>
                        <th class="num" style="width:100px">{{ __('reports.debit') }}</th>
                        <th class="num" style="width:100px">{{ __('reports.credit') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tx['entries'] as $e)
                        <tr>
                            <td><strong>{{ $e['akun'] }}</strong></td>
                            <td>{{ $e['nama_akun'] }}</td>
                            <td class="num">{{ $e['debit'] > 0 ? FormatHelper::rupiah($e['debit']) : '—' }}</td>
                            <td class="num">{{ $e['kredit'] > 0 ? FormatHelper::rupiah($e['kredit']) : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
@empty
    <p class="muted" style="text-align:center;padding:24px">{{ __('reports.not_found') }}</p>
@endforelse
@endsection
