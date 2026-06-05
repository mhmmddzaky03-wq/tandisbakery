@extends('pdf.layout')

@php use App\Support\FormatHelper; @endphp

@section('pdf-body')
<p class="report-subtitle" style="margin-bottom:12px;">{{ __('reports.coa.pdf_subtitle', ['count' => $accounts->count()]) }}</p>

<table class="data-table">
    <thead>
        <tr>
            <th style="width:52px">{{ __('coa.col_code') }}</th>
            <th>{{ __('coa.col_name') }}</th>
            <th class="center" style="width:28px">{{ __('reports.pos') }}</th>
            <th style="width:72px">{{ __('coa.col_group') }}</th>
            <th style="width:100px">{{ __('coa.col_subgroup') }}</th>
            <th class="num" style="width:110px">{{ __('reports.balance') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($accounts as $row)
            @php $acc = $row['account']; @endphp
            <tr>
                <td><strong>{{ $acc->kode }}</strong></td>
                <td>{{ $acc->nama }}</td>
                <td class="center">{{ $acc->posisi === 'Credit' ? __('reports.position_cr') : __('reports.position_dr') }}</td>
                <td>{{ $acc->grup }}</td>
                <td>{{ $acc->sub_grup }}</td>
                <td class="num">{{ FormatHelper::rupiah($row['saldo']) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
