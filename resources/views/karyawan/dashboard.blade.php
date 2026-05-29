@extends('layouts.app')

@php
    $title = 'Dashboard Employee - Tandi\'s Bakery';
    $role = 'karyawan';
    $active = 'karyawan.dashboard';
    $pageTitle = 'Dashboard';
    $pageSubtitle = 'Pantau ringkasan operasional dan keuangan bakery';

    $ic = fn ($d) => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'" /></svg>';
@endphp

@section('content')
    <div>
        <div class="grid gap-4 lg:grid-cols-3">
            <x-kpi-card title="Total Produksi" value="0" sub="No input yet" trend="+0.0%" tone="blue" :icon="$ic('M6 21h12a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-1l-1-2H8L7 7H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z')" />
            <x-kpi-card title="Total Penjualan Hari Ini" value="Rp 0" sub="No transactions yet" trend="+0.0%" tone="green" :icon="$ic('M4 7h16M4 11h16M8 15h4M6 19h12')" />
            <x-kpi-card title="Input Data Persediaan" value="Safe" sub="Monitor min. stock" trend="-0.0%" tone="amber" :icon="$ic('M7 7h10M7 12h10M7 17h10M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z')" />
        </div>

        <div class="mt-5 bakery-card">
            <div class="bakery-card-header">
                <div class="text-lg font-extrabold text-slate-900">Produksi Terbaru</div>
            </div>
            <div class="bakery-card-body">
                <div class="grid gap-3">
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-black/5">
                        <div>
                            <div class="text-sm font-extrabold text-slate-800">Input Data Produksi</div>
                            <div class="mt-1 text-xs font-semibold text-slate-400">No data yet</div>
                        </div>
                        <a class="bakery-btn-primary px-4 py-2 text-xs font-extrabold" href="{{ route('karyawan.produksi') }}">Input</a>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-black/5">
                        <div>
                            <div class="text-sm font-extrabold text-slate-800">Input Data Penjualan</div>
                            <div class="mt-1 text-xs font-semibold text-slate-400">No data yet</div>
                        </div>
                        <a class="bakery-btn-primary px-4 py-2 text-xs font-extrabold" href="{{ route('karyawan.penjualan') }}">Input</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
