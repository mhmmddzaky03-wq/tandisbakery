@extends('layouts.app')

@php
    $title = 'Dashboard Admin - Tandi\'s Bakery';
    $role = 'admin';
    $active = 'admin.dashboard';
    $pageTitle = __('nav.dashboard');
    $subtitle = __('nav.main_menu');

    $ic = fn ($d) => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'" /></svg>';
@endphp

@section('content')
    <div class="pt-6">
        <div class="grid gap-4 lg:grid-cols-4">
            <x-kpi-card title="{{ __('page.total_stock_items') }}" value="13" sub="0 {{ __('page.below_min') }}" trend="-5.2%" tone="rose" :icon="$ic('M7 7h10M7 12h10M7 17h10M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z')" />
            <x-kpi-card title="{{ __('page.production_success_dash') }}" value="3" sub="{{ __('page.from_total') }}" trend="+12.5%" tone="green" :icon="$ic('M7 12l3 3 7-7')" />
            <x-kpi-card title="{{ __('page.operational_cost_dash') }}" value="Rp 55.4jt" sub="{{ __('page.this_month') }}" trend="+8.3%" tone="blue" :icon="$ic('M12 3v18M17 8l-5-5-5 5')" />
            <x-kpi-card title="{{ __('page.total_sales_dash') }}" value="Rp 72.6jt" sub="{{ __('page.last_30_days') }}" trend="+28.4%" tone="amber" :icon="$ic('M4 7h16M4 11h16M8 15h4M6 19h12')" />
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-3">
            <div class="bakery-card lg:col-span-2">
                <div class="bakery-card-header">
                    <div class="text-sm font-bold text-slate-800">{{ __('page.sales_trend') }}</div>
                </div>
                <div class="bakery-card-body">
                    <div class="rounded-2xl bg-white">
                        <svg viewBox="0 0 900 260" class="h-[240px] w-full">
                            <rect x="0" y="0" width="900" height="260" rx="16" fill="#fff" />
                            <line x1="50" y1="210" x2="860" y2="210" stroke="#E5E7EB" />
                            <line x1="50" y1="160" x2="860" y2="160" stroke="#F1F5F9" />
                            <line x1="50" y1="110" x2="860" y2="110" stroke="#F1F5F9" />
                            <line x1="50" y1="60" x2="860" y2="60" stroke="#F1F5F9" />
                            <path d="M60 180 C120 110, 160 200, 220 150 C280 90, 320 170, 380 140 C440 110, 500 180, 560 120 C620 60, 660 160, 720 110 C780 70, 820 120, 850 80" fill="none" stroke="#f59e0b" stroke-width="4" />
                            <circle cx="60" cy="180" r="5" fill="#f59e0b" />
                            <circle cx="220" cy="150" r="5" fill="#f59e0b" />
                            <circle cx="380" cy="140" r="5" fill="#f59e0b" />
                            <circle cx="560" cy="120" r="5" fill="#f59e0b" />
                            <circle cx="720" cy="110" r="5" fill="#f59e0b" />
                            <circle cx="850" cy="80" r="5" fill="#f59e0b" />
                            <text x="55" y="245" font-size="12" fill="#94A3B8" font-family="Poppins">2 Jun</text>
                            <text x="165" y="245" font-size="12" fill="#94A3B8" font-family="Poppins">5 Jun</text>
                            <text x="275" y="245" font-size="12" fill="#94A3B8" font-family="Poppins">9 Jun</text>
                            <text x="385" y="245" font-size="12" fill="#94A3B8" font-family="Poppins">12 Jun</text>
                            <text x="495" y="245" font-size="12" fill="#94A3B8" font-family="Poppins">16 Jun</text>
                            <text x="605" y="245" font-size="12" fill="#94A3B8" font-family="Poppins">20 Jun</text>
                            <text x="715" y="245" font-size="12" fill="#94A3B8" font-family="Poppins">24 Jun</text>
                            <text x="825" y="245" font-size="12" fill="#94A3B8" font-family="Poppins">30 Jun</text>
                        </svg>
                        <div class="-mt-2 text-center text-xs font-bold text-amber-600">{{ __('page.sales_chart_label') }}</div>
                    </div>
                </div>
            </div>

            <div class="bakery-card">
                <div class="bakery-card-header">
                    <div class="text-sm font-bold text-slate-800">{{ __('page.cost_composition') }}</div>
                </div>
                <div class="bakery-card-body">
                    <svg viewBox="0 0 380 260" class="h-[240px] w-full">
                        <rect x="0" y="0" width="380" height="260" rx="16" fill="#fff" />
                        <line x1="50" y1="210" x2="340" y2="210" stroke="#E5E7EB" />
                        <rect x="90" y="120" width="70" height="90" rx="10" fill="#f59e0b" opacity="0.75" />
                        <rect x="220" y="80" width="70" height="130" rx="10" fill="#f59e0b" />
                        <text x="95" y="238" font-size="12" fill="#94A3B8" font-family="Poppins">{{ __('page.fixed_label') }}</text>
                        <text x="210" y="238" font-size="12" fill="#94A3B8" font-family="Poppins">{{ __('page.variable_label') }}</text>
                    </svg>
                    <div class="-mt-2 text-center text-xs font-bold text-amber-600">{{ __('page.cost_chart_label') }}</div>
                </div>
            </div>
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-3">
            <div class="bakery-card lg:col-span-2">
                <div class="bakery-card-header">
                    <div class="text-sm font-bold text-slate-800">{{ __('page.financial_summary') }}</div>
                </div>
                <div class="bakery-card-body">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="bakery-card p-5 ring-0 bg-slate-50">
                            <div class="text-xs font-bold text-slate-400">{{ __('page.total_label') }}</div>
                            <div class="mt-1 text-lg font-extrabold text-slate-800">Rp82,1jt</div>
                            <div class="mt-4 h-28 rounded-2xl bg-white ring-1 ring-black/5 grid place-items-center">
                                <svg viewBox="0 0 120 120" class="h-24 w-24">
                                    <circle cx="60" cy="60" r="42" stroke="#e5e7eb" stroke-width="14" fill="none" />
                                    <circle cx="60" cy="60" r="42" stroke="#22c55e" stroke-width="14" fill="none" stroke-dasharray="140 400" transform="rotate(-90 60 60)" />
                                    <circle cx="60" cy="60" r="42" stroke="#ef4444" stroke-width="14" fill="none" stroke-dasharray="95 400" stroke-dashoffset="-140" transform="rotate(-90 60 60)" />
                                    <circle cx="60" cy="60" r="42" stroke="#f59e0b" stroke-width="14" fill="none" stroke-dasharray="75 400" stroke-dashoffset="-235" transform="rotate(-90 60 60)" />
                                </svg>
                            </div>
                        </div>

                        <div class="bakery-card p-5 ring-0 bg-slate-50">
                            <div class="flex items-center justify-between">
                                <div class="text-xs font-bold text-slate-400">{{ __('page.net_profit_dash') }}</div>
                                <div class="text-xs font-bold text-emerald-600">Juni 2025</div>
                            </div>
                            <div class="mt-2 text-2xl font-extrabold text-emerald-600">Rp26,7jt</div>
                        </div>

                        <div class="bakery-card p-5 ring-0 bg-slate-50">
                            <div class="flex items-center justify-between">
                                <div class="text-xs font-bold text-slate-400">{{ __('page.cost_label') }}</div>
                                <div class="text-xs font-bold text-amber-600">Juni 2025</div>
                            </div>
                            <div class="mt-2 text-2xl font-extrabold text-amber-600">Rp32,6jt</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bakery-card">
                <div class="bakery-card-header">
                    <div class="text-sm font-bold text-slate-800">{{ __('page.low_stock_warning') }}</div>
                </div>
                <div class="bakery-card-body">
                    <div class="grid gap-4">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs font-bold text-rose-600">{{ __('page.low_stock_warning') }}</div>
                            <div class="mt-1 text-sm font-semibold text-slate-600">{{ __('page.all_stock_safe') }}</div>
                            <div class="mt-1 text-xs font-semibold text-slate-400">{{ __('page.no_restock_needed') }}</div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs font-bold text-emerald-600">{{ __('page.recent_production') }}</div>
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 ring-1 ring-black/5">
                                    <div>
                                        <div class="text-sm font-bold text-slate-800">Roti Tawar</div>
                                        <div class="text-xs font-semibold text-slate-400">60 loyang • 15 Apr</div>
                                    </div>
                                    <span class="bakery-badge bg-emerald-50 text-emerald-600">{{ __('page.success_label') }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 ring-1 ring-black/5">
                                    <div>
                                        <div class="text-sm font-bold text-slate-800">Croissant</div>
                                        <div class="text-xs font-semibold text-slate-400">100 pcs • 15 Apr</div>
                                    </div>
                                    <span class="bakery-badge bg-emerald-50 text-emerald-600">{{ __('page.success_label') }}</span>
                                </div>
                                <div class="flex items-center justify-between rounded-xl bg-white px-3 py-2 ring-1 ring-black/5">
                                    <div>
                                        <div class="text-sm font-bold text-slate-800">Kue Ulang Tahun</div>
                                        <div class="text-xs font-semibold text-slate-400">5 pcs • 14 Apr</div>
                                    </div>
                                    <span class="bakery-badge bg-emerald-50 text-emerald-600">{{ __('page.success_label') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
