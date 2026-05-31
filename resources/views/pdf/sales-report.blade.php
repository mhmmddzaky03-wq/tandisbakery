@extends('pdf.layout')

@php use App\Support\FormatHelper; @endphp

@section('pdf-body')
<table class="summary-grid">
    <tr>
        <td colspan="3" class="highlight">
            <div class="summary-label">Total penjualan</div>
            <div class="summary-value">{{ FormatHelper::rupiah($total) }}</div>
            <div style="font-size:8pt;color:#64748b;margin-top:4px">{{ $sales->count() }} transaksi</div>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width:72px">ID</th>
            <th style="width:88px">Tanggal</th>
            <th class="num">Total</th>
            <th style="width:80px">Metode</th>
            <th class="center" style="width:48px">Jml</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $s)
            <tr>
                <td><strong>{{ $s->id }}</strong></td>
                <td>{{ FormatHelper::dateId($s->tanggal) }}</td>
                <td class="num">{{ FormatHelper::rupiah($s->total) }}</td>
                <td>{{ $s->metode }}</td>
                <td class="center">{{ $s->jumlah }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td colspan="2">TOTAL</td>
            <td class="num">{{ FormatHelper::rupiah($total) }}</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>
@endsection
