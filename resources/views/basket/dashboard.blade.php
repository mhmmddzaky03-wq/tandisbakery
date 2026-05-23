@extends('layouts.app')

@php
    $title = 'Dashboard Basket - Tandi\'s Bakery';
    $role = 'basket';
    $active = 'basket.dashboard';
    $pageTitle = 'Dashboard Basket';
    $subtitle = 'Halaman Basket';
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">Dashboard Basket</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">Tampilan dashboard basket sesuai desain</div>
                </div>
            </div>
            <div class="bakery-card-body">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="bakery-card p-5 ring-0 bg-slate-50">
                        <div class="text-xs font-bold text-slate-400">Order Hari Ini</div>
                        <div class="mt-2 text-2xl font-extrabold text-slate-900">0</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-emerald-50">
                        <div class="text-xs font-bold text-slate-500">Selesai</div>
                        <div class="mt-2 text-2xl font-extrabold text-emerald-700">0</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-amber-50">
                        <div class="text-xs font-bold text-slate-500">Pending</div>
                        <div class="mt-2 text-2xl font-extrabold text-amber-700">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

