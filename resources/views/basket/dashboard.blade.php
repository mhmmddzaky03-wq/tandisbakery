@extends('layouts.app')
@php $title='Dashboard Basket'; $role='basket'; $active='basket.dashboard'; $pageTitle='Dashboard Basket'; $subtitle='Basket'; @endphp
@section('content')
<div class="pt-6 bakery-card p-6">
    <div class="text-lg font-extrabold mb-4">Dashboard Basket</div>
    <div class="grid gap-4 md:grid-cols-3">
        <div class="bakery-card p-5 bg-slate-50"><div class="text-xs text-slate-400">Order Hari Ini</div><div class="text-2xl font-extrabold">{{ $orderCount }}</div></div>
        <div class="bakery-card p-5 bg-emerald-50"><div class="text-xs">Selesai</div><div class="text-2xl font-extrabold text-emerald-700">{{ $completed }}</div></div>
        <div class="bakery-card p-5 bg-amber-50"><div class="text-xs">Pending</div><div class="text-2xl font-extrabold text-amber-700">{{ $pending }}</div></div>
    </div>
</div>
@endsection
