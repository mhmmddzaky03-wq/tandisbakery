@extends('pdf.layout')

@php use App\Support\FormatHelper; @endphp

@section('pdf-body')
<table class="summary-grid">
    <tr>
        <td>
            <div class="summary-label">{{ __('reports.income_statement.revenue') }}</div>
            <div class="summary-value">{{ FormatHelper::rupiah($data['sales']) }}</div>
        </td>
        <td>
            <div class="summary-label">{{ __('reports.income_statement.gross_profit') }}</div>
            <div class="summary-value">{{ FormatHelper::rupiah($data['gross_profit']) }}</div>
        </td>
        <td class="highlight">
            <div class="summary-label">{{ __('reports.income_statement.net_profit') }}</div>
            <div class="summary-value">{{ FormatHelper::rupiah($data['net_profit']) }}</div>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th>{{ __('reports.pos') }}</th>
            <th class="num" style="width:130px">{{ __('reports.amount') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr class="total">
            <td colspan="2">{{ __('reports.income_statement.section_revenue') }}</td>
        </tr>
        @foreach ($data['revenue_lines'] as $line)
            <tr>
                <td style="padding-left:16px">{{ $line['kode'] }} — {{ $line['nama'] }}</td>
                <td class="num">{{ FormatHelper::rupiah($line['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td>{{ __('reports.income_statement.total_revenue') }}</td>
            <td class="num">{{ FormatHelper::rupiah($data['sales']) }}</td>
        </tr>

        <tr class="total">
            <td colspan="2" style="padding-top:8px">{{ __('reports.income_statement.section_cogs') }}</td>
        </tr>
        <tr>
            <td style="padding-left:16px">5-110 — Cost of Goods Sold</td>
            <td class="num">{{ $data['cogs'] > 0 ? FormatHelper::rupiah($data['cogs']) : '—' }}</td>
        </tr>
        <tr class="total">
            <td>{{ __('reports.income_statement.section_gross') }}</td>
            <td class="num">{{ FormatHelper::rupiah($data['gross_profit']) }}</td>
        </tr>

        <tr class="total">
            <td colspan="2" style="padding-top:8px">{{ __('reports.income_statement.section_operating') }}</td>
        </tr>
        @foreach ($data['operating_expense_lines'] as $line)
            <tr>
                <td style="padding-left:16px">{{ $line['kode'] }} — {{ $line['nama'] }}</td>
                <td class="num">{{ FormatHelper::rupiah($line['amount']) }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td>{{ __('reports.income_statement.total_operating') }}</td>
            <td class="num">{{ FormatHelper::rupiah($data['total_operating_expenses']) }}</td>
        </tr>

        <tr>
            <td>{{ __('reports.income_statement.before_tax') }}</td>
            <td class="num">{{ FormatHelper::rupiah($data['income_before_tax']) }}</td>
        </tr>
        <tr>
            <td style="padding-left:16px">5-190 — Income Tax</td>
            <td class="num">{{ $data['tax'] > 0 ? FormatHelper::rupiah($data['tax']) : '—' }}</td>
        </tr>
        <tr class="total">
            <td>{{ __('reports.income_statement.section_net') }}</td>
            <td class="num">{{ FormatHelper::rupiah($data['net_profit']) }}</td>
        </tr>
    </tbody>
</table>
@endsection
