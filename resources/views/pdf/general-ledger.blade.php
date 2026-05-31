@extends('pdf.layout')

@php
    use App\Support\FormatHelper;
    $positionLabel = $account->posisi === 'Credit' ? 'Cr' : 'Dr';
@endphp

@section('pdf-body')
<p class="report-subtitle" style="margin-bottom:12px;">
    <strong>{{ $account->kode }}</strong> — {{ $account->nama }}
    · {{ $account->grup }} · Posisi {{ $positionLabel }}
</p>

<table class="summary-grid">
    <tr>
        <td>
            <div class="summary-label">Saldo awal</div>
            <div class="summary-value">{{ FormatHelper::glBalance($opening_balance) }}</div>
        </td>
        <td>
            <div class="summary-label">Mutasi</div>
            <div class="summary-value">{{ $rows->where(fn ($r) => ! ($r['is_opening'] ?? false))->count() }} baris</div>
        </td>
        <td class="highlight">
            <div class="summary-label">Saldo akhir</div>
            <div class="summary-value">{{ FormatHelper::glBalance($closing_balance) }}</div>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th class="center" style="width:32px">No</th>
            <th style="width:72px">Tanggal</th>
            <th>Ref</th>
            <th class="num" style="width:96px">Debit</th>
            <th class="num" style="width:96px">Kredit</th>
            <th class="num" style="width:104px">Saldo</th>
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
            <td colspan="3">Saldo akhir periode</td>
            <td colspan="2"></td>
            <td class="num">{{ FormatHelper::glBalance($closing_balance) }}</td>
        </tr>
    </tbody>
</table>
@endsection
