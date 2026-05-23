@extends('layouts.app')
@php use App\Support\FormatHelper; $role='admin'; $active='admin.gl'; $pageTitle=__('nav.general_ledger'); $subtitle=__('nav.accounting'); @endphp
@section('content')
<div class="pt-6 bakery-card p-6">
    <div class="flex justify-between mb-4"><div class="text-lg font-extrabold">{{ __('nav.general_ledger') }}</div><button type="button" class="bakery-btn-ghost" data-print>Cetak</button></div>
    <form method="GET" class="flex flex-wrap gap-3 mb-4">
        <select name="account" class="bakery-input">@foreach($accounts as $a)<option value="{{ $a->kode }}" @selected($accountKode === $a->kode)>{{ $a->kode }} — {{ $a->nama }}</option>@endforeach</select>
        <input type="date" name="from" value="{{ $from }}" class="bakery-input" />
        <input type="date" name="to" value="{{ $to }}" class="bakery-input" />
        <button class="bakery-btn-primary">Tampilkan</button>
    </form>
    <p class="mb-2 font-bold">{{ $account->nama }} ({{ $account->kode }}) — Saldo {{ FormatHelper::rupiah($balance) }}</p>
    <table class="bakery-table"><thead><tr><th>No</th><th>Tanggal</th><th>Ref</th><th>Debit</th><th>Kredit</th><th>Saldo</th></tr></thead>
    <tbody>@foreach($rows as $r)<tr><td>{{ $r['no'] }}</td><td>{{ $r['tgl'] }}</td><td>{{ $r['ref'] }}</td><td>{{ $r['debit'] ? FormatHelper::rupiah($r['debit']) : '-' }}</td><td>{{ $r['kredit'] ? FormatHelper::rupiah($r['kredit']) : '-' }}</td><td>{{ FormatHelper::rupiah($r['saldo']) }}</td></tr>@endforeach</tbody></table>
</div>
@endsection
