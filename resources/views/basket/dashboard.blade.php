@extends('layouts.app')

@php
    $title = 'Dashboard Basket';
    $role = 'basket';
    $active = 'basket.dashboard';
    $pageTitle = 'Dashboard Basket';
    $pageSubtitle = 'Ringkasan pesanan basket';
@endphp

@section('content')
<div class="bakery-card">
    <div class="bakery-card-body grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-slate-50 p-5"><div class="text-xs text-slate-400">Order Hari Ini</div><div class="text-2xl font-extrabold">{{ $orderCount }}</div></div>
        <div class="rounded-2xl bg-emerald-50 p-5"><div class="text-xs">Selesai</div><div class="text-2xl font-extrabold text-emerald-700">{{ $completed }}</div></div>
        <div class="rounded-2xl bg-amber-50 p-5"><div class="text-xs">Pending</div><div class="text-2xl font-extrabold text-amber-700">{{ $pending }}</div></div>
    </div>
</div>
@endsection
