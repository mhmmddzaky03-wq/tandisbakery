@extends('pdf.layout')

@php use App\Support\FormatHelper; @endphp

@section('pdf-body')
<table class="summary-grid">
    <tr>
        <td>
            <div class="summary-label">{{ __('reports.balance_sheet.total_assets') }}</div>
            <div class="summary-value">{{ FormatHelper::rupiah($data['total_assets']) }}</div>
        </td>
        <td>
            <div class="summary-label">{{ __('reports.balance_sheet.liabilities') }}</div>
            <div class="summary-value">{{ FormatHelper::rupiah($data['total_liabilities']) }}</div>
        </td>
        <td class="highlight">
            <div class="summary-label">{{ __('reports.balance_sheet.equity') }}</div>
            <div class="summary-value">{{ FormatHelper::rupiah($data['total_equity']) }}</div>
        </td>
    </tr>
</table>

@php
    $columns = [
        ['title' => __('reports.balance_sheet.pdf_section_assets'), 'sections' => $data['assets'], 'total_label' => __('reports.balance_sheet.pdf_total_assets'), 'total' => $data['total_assets']],
        ['title' => __('reports.balance_sheet.pdf_section_liabilities_equity'), 'sections' => $data['liabilities']->concat($data['equity']), 'total_label' => __('reports.balance_sheet.pdf_total_liabilities_equity'), 'total' => $data['total_liabilities_equity']],
    ];
@endphp

@foreach ($columns as $column)
    <p style="font-weight:bold;font-size:11pt;margin:16px 0 8px;color:#b45309">{{ $column['title'] }}</p>

    @forelse ($column['sections'] as $section)
        <p style="font-weight:600;font-size:9pt;margin:10px 0 4px;color:#64748b">{{ $section['label'] }}</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:52px">{{ __('reports.trial_balance.col_code') }}</th>
                    <th>{{ __('reports.account') }}</th>
                    <th class="num" style="width:120px">{{ __('reports.balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($section['lines'] as $line)
                    <tr>
                        <td><strong>{{ $line['kode'] }}</strong></td>
                        <td>{{ $line['nama'] }}</td>
                        <td class="num">{{ FormatHelper::rupiah($line['saldo']) }}</td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td colspan="2">{{ __('reports.subtotal_label', ['label' => $section['label']]) }}</td>
                    <td class="num">{{ FormatHelper::rupiah($section['subtotal']) }}</td>
                </tr>
            </tbody>
        </table>
    @empty
        <p style="font-size:9pt;color:#94a3b8;margin:8px 0">{{ __('reports.no_balance') }}</p>
    @endforelse

    <table class="data-table" style="margin-top:8px">
        <tbody>
            <tr class="total">
                <td>{{ $column['total_label'] }}</td>
                <td class="num" style="width:120px">{{ FormatHelper::rupiah($column['total']) }}</td>
            </tr>
        </tbody>
    </table>
@endforeach

@if (! $data['is_balanced'])
    <p style="margin-top:12px;font-size:9pt;color:#b91c1c">
        {{ __('reports.balance_sheet.pdf_difference', ['amount' => FormatHelper::rupiah($data['difference'])]) }}
    </p>
@endif
@endsection
