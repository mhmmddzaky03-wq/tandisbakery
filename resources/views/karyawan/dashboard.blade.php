@extends('layouts.app')

@php
    use App\Support\FormatHelper;

    $title = __('app.pages.dashboard').' - Tandi\'s Bakery';
    $role = 'karyawan';
    $active = 'karyawan.dashboard';
    $pageTitle = __('app.pages.dashboard');
    $pageSubtitle = __('app.pages.dashboard_subtitle_employee');

    $ic = fn ($d) => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'" /></svg>';
@endphp

@section('content')
    <div class="bakery-page">
        <div class="grid gap-4 sm:grid-cols-2">
            <x-kpi-card
                :title="__('dashboard.employee.total_production')"
                :value="(string) $totalProduction"
                :sub="__('dashboard.employee.all_production_data')"
                tone="blue"
                :icon="$ic('M6 21h12a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-1l-1-2H8L7 7H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z')"
            />
            <x-kpi-card
                :title="__('dashboard.employee.today_sales')"
                :value="FormatHelper::rupiah($todaySales)"
                :sub="__('dashboard.employee.today_sales_sub')"
                tone="green"
                :icon="$ic('M4 7h16M4 11h16M8 15h4M6 19h12')"
            />
        </div>

        <div class="bakery-card">
            <div class="bakery-card-header bakery-card-header--bordered">
                <div class="bakery-card-header__title">{{ __('app.pages.quick_access') }}</div>
            </div>
            <div class="bakery-card-body">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-black/5">
                        <div>
                            <div class="text-sm font-extrabold text-slate-800">{{ __('dashboard.employee.production_card') }}</div>
                            <div class="mt-1 text-xs font-semibold text-slate-400">
                                @if ($latestProduction)
                                    {{ __('dashboard.employee.last_production', ['info' => FormatHelper::dateId($latestProduction->tanggal).' — '.$latestProduction->product_name]) }}
                                @else
                                    {{ __('dashboard.employee.no_production') }}
                                @endif
                            </div>
                        </div>
                        <a class="bakery-btn-primary px-4 py-2 text-xs font-extrabold" href="{{ route('karyawan.produksi') }}">{{ __('app.common.open') }}</a>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-black/5">
                        <div>
                            <div class="text-sm font-extrabold text-slate-800">{{ __('dashboard.employee.sales_card') }}</div>
                            <div class="mt-1 text-xs font-semibold text-slate-400">{{ __('dashboard.employee.sales_card_sub') }}</div>
                        </div>
                        <a class="bakery-btn-primary px-4 py-2 text-xs font-extrabold" href="{{ route('karyawan.penjualan') }}">{{ __('app.common.open') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
