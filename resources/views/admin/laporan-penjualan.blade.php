@extends('layouts.app')
@php use App\Support\FormatHelper; $role='admin'; $active='admin.laporan_penjualan'; $pageTitle=__('nav.sales_report'); $subtitle=__('nav.financial_reports'); @endphp
@section('content')
<div class="pt-6 bakery-card">
    <div class="bakery-card-header"><div class="text-lg font-extrabold">{{ __('nav.sales_report') }}</div><button type="button" class="bakery-btn-ghost" data-print>Cetak</button></div>
    <div class="bakery-card-body">
        <p class="mb-4 font-extrabold text-emerald-600">Total: {{ FormatHelper::rupiah($total) }}</p>
        <table class="bakery-table"><thead><tr><th>ID</th><th>Tanggal</th><th>Total</th><th>Metode</th></tr></thead>
        <tbody>@foreach($sales as $s)<tr><td>{{ $s->id }}</td><td>{{ FormatHelper::dateId($s->tanggal) }}</td><td>{{ FormatHelper::rupiah($s->total) }}</td><td>{{ $s->metode }}</td></tr>@endforeach</tbody></table>
    </div>
</div>
@endsection
