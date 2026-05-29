@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.laba_rugi';
    $pageTitle = 'Laba Rugi';
    $pageSubtitle = 'Laporan Laba Rugi';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-ghost whitespace-nowrap" data-print>Cetak</button>
@endpush

@section('content')
<div class="bakery-card">
    <div class="bakery-card-body grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-slate-50 p-4"><div class="text-xs text-slate-400">Penjualan</div><div class="mt-1 text-xl font-extrabold text-emerald-600">{{ FormatHelper::rupiah($data['sales']) }}</div></div>
        <div class="rounded-2xl bg-slate-50 p-4"><div class="text-xs text-slate-400">Penjualan Bersih</div><div class="mt-1 text-xl font-extrabold text-emerald-600">{{ FormatHelper::rupiah($data['net_sales']) }}</div></div>
        <div class="rounded-2xl bg-slate-50 p-4"><div class="text-xs text-slate-400">LABA KOTOR</div><div class="mt-1 text-xl font-extrabold text-sky-600">{{ FormatHelper::rupiah($data['gross_profit']) }}</div></div>
        <div class="rounded-2xl bg-slate-50 p-4"><div class="text-xs text-slate-400">LABA BERSIH</div><div class="mt-1 text-xl font-extrabold text-amber-600">{{ FormatHelper::rupiah($data['net_profit']) }}</div></div>
    </div>
</div>
@endsection
