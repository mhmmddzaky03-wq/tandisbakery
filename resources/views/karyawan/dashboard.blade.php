@extends('layouts.app')

@php
    use App\Support\FormatHelper;

    $title = 'Dashboard Karyawan - Tandi\'s Bakery';
    $role = 'karyawan';
    $active = 'karyawan.dashboard';
    $pageTitle = 'Dashboard';
    $pageSubtitle = 'Ringkasan data produksi dan penjualan';

    $ic = fn ($d) => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'" /></svg>';
@endphp

@section('content')
    <div class="bakery-page">
        <div class="grid gap-4 sm:grid-cols-2">
            <x-kpi-card
                title="Total Produksi"
                :value="(string) $totalProduction"
                sub="Semua data produksi"
                tone="blue"
                :icon="$ic('M6 21h12a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-1l-1-2H8L7 7H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z')"
            />
            <x-kpi-card
                title="Penjualan Hari Ini"
                :value="FormatHelper::rupiah($todaySales)"
                sub="Total transaksi hari ini"
                tone="green"
                :icon="$ic('M4 7h16M4 11h16M8 15h4M6 19h12')"
            />
        </div>

        <div class="bakery-card">
            <div class="bakery-card-header bakery-card-header--bordered">
                <div class="bakery-card-header__title">Akses Cepat</div>
            </div>
            <div class="bakery-card-body">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-black/5">
                        <div>
                            <div class="text-sm font-extrabold text-slate-800">Data Produksi</div>
                            <div class="mt-1 text-xs font-semibold text-slate-400">
                                @if ($latestProduction)
                                    Terakhir: {{ FormatHelper::dateId($latestProduction->tanggal) }} — {{ $latestProduction->product_name }}
                                @else
                                    Belum ada data produksi
                                @endif
                            </div>
                        </div>
                        <a class="bakery-btn-primary px-4 py-2 text-xs font-extrabold" href="{{ route('karyawan.produksi') }}">Buka</a>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-black/5">
                        <div>
                            <div class="text-sm font-extrabold text-slate-800">Transaksi Penjualan</div>
                            <div class="mt-1 text-xs font-semibold text-slate-400">Input dan lihat transaksi penjualan</div>
                        </div>
                        <a class="bakery-btn-primary px-4 py-2 text-xs font-extrabold" href="{{ route('karyawan.penjualan') }}">Buka</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
