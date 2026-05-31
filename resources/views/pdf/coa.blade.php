@extends('pdf.layout')

@php use App\Support\FormatHelper; @endphp

@section('pdf-body')
<p class="report-subtitle" style="margin-bottom:12px;">{{ $accounts->count() }} akun · Saldo kumulatif dari jurnal</p>

<table class="data-table">
    <thead>
        <tr>
            <th style="width:52px">Kode</th>
            <th>Nama Akun</th>
            <th class="center" style="width:28px">Pos</th>
            <th style="width:72px">Grup</th>
            <th style="width:100px">Sub-Grup</th>
            <th class="num" style="width:110px">Saldo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($accounts as $row)
            @php $acc = $row['account']; @endphp
            <tr>
                <td><strong>{{ $acc->kode }}</strong></td>
                <td>{{ $acc->nama }}</td>
                <td class="center">{{ $acc->posisi === 'Credit' ? 'Cr' : 'Dr' }}</td>
                <td>{{ $acc->grup }}</td>
                <td>{{ $acc->sub_grup }}</td>
                <td class="num">{{ FormatHelper::rupiah($row['saldo']) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
