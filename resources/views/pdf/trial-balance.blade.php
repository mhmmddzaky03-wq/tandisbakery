@extends('pdf.layout')

@php use App\Support\FormatHelper; @endphp

@section('pdf-body')
<table class="summary-grid">
    <tr>
        <td>
            <div class="summary-label">Total Debit</div>
            <div class="summary-value">{{ FormatHelper::rupiah($totalDebit) }}</div>
        </td>
        <td>
            <div class="summary-label">Total Kredit</div>
            <div class="summary-value">{{ FormatHelper::rupiah($totalKredit) }}</div>
        </td>
        <td class="highlight">
            <div class="summary-label">Selisih</div>
            <div class="summary-value">{{ FormatHelper::rupiahTb($difference) }}</div>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width:52px">Kode</th>
            <th>Akun</th>
            <th class="center" style="width:28px">Pos</th>
            <th style="width:70px">Grup</th>
            <th class="num" style="width:88px">Debit</th>
            <th class="num" style="width:88px">Kredit</th>
            <th class="num" style="width:88px">SubTotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            @php
                $acc = $row['account'];
                $pos = $acc->posisi === 'Credit' ? 'Cr' : 'Dr';
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
            <td colspan="4">TOTAL</td>
            <td class="num">{{ FormatHelper::rupiahTb($totalDebit) }}</td>
            <td class="num">{{ FormatHelper::rupiahTb($totalKredit) }}</td>
            <td class="num">{{ FormatHelper::rupiahTb($difference) }}</td>
        </tr>
    </tbody>
</table>
@endsection
