@extends('pdf.layout')

@php use App\Support\FormatHelper; @endphp

@section('pdf-body')
<table class="summary-grid">
    <tr>
        <td>
            <div class="summary-label">Pendapatan</div>
            <div class="summary-value">{{ FormatHelper::rupiah($data['sales']) }}</div>
        </td>
        <td>
            <div class="summary-label">Laba kotor</div>
            <div class="summary-value">{{ FormatHelper::rupiah($data['gross_profit']) }}</div>
        </td>
        <td class="highlight">
            <div class="summary-label">Laba bersih</div>
            <div class="summary-value">{{ FormatHelper::rupiah($data['net_profit']) }}</div>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th>Pos</th>
            <th class="num" style="width:130px">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <tr class="total">
            <td colspan="2">PENDAPATAN</td>
        </tr>
        @foreach ($data['revenue_lines'] as $line)
            <tr>
                <td style="padding-left:16px">{{ $line['kode'] }} — {{ $line['nama'] }}</td>
                <td class="num">{{ FormatHelper::rupiah($line['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td>Total pendapatan</td>
            <td class="num">{{ FormatHelper::rupiah($data['sales']) }}</td>
        </tr>

        <tr class="total">
            <td colspan="2" style="padding-top:8px">HARGA POKOK PENJUALAN</td>
        </tr>
        <tr>
            <td style="padding-left:16px">5-110 — Cost of Goods Sold</td>
            <td class="num">{{ $data['cogs'] > 0 ? FormatHelper::rupiah($data['cogs']) : '—' }}</td>
        </tr>
        <tr class="total">
            <td>LABA KOTOR</td>
            <td class="num">{{ FormatHelper::rupiah($data['gross_profit']) }}</td>
        </tr>

        <tr class="total">
            <td colspan="2" style="padding-top:8px">BEBAN OPERASIONAL</td>
        </tr>
        @foreach ($data['operating_expense_lines'] as $line)
            <tr>
                <td style="padding-left:16px">{{ $line['kode'] }} — {{ $line['nama'] }}</td>
                <td class="num">{{ FormatHelper::rupiah($line['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td>Total beban operasional</td>
            <td class="num">{{ FormatHelper::rupiah($data['total_operating_expenses']) }}</td>
        </tr>

        <tr>
            <td>Laba sebelum pajak</td>
            <td class="num">{{ FormatHelper::rupiah($data['income_before_tax']) }}</td>
        </tr>
        <tr>
            <td style="padding-left:16px">5-190 — Income Tax</td>
            <td class="num">{{ $data['tax'] > 0 ? FormatHelper::rupiah($data['tax']) : '—' }}</td>
        </tr>
        <tr class="total">
            <td>LABA BERSIH</td>
            <td class="num">{{ FormatHelper::rupiah($data['net_profit']) }}</td>
        </tr>
    </tbody>
</table>
@endsection
