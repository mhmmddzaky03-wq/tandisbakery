@extends('layouts.app')

@php
    $title = __('nav.income_statement') . ' - Admin';
    $role = 'admin';
    $active = 'admin.laba_rugi';
    $pageTitle = __('nav.income_statement');
    $subtitle = __('nav.financial_reports');
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('nav.income_statement') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.is_subtitle') }} - 1 Juni 2025 s/d 30 Juni 2025</div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="bakery-btn-ghost px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.print') }}</button>
                    <button class="bakery-btn-primary px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.download_pdf') }}</button>
                </div>
            </div>

            <div class="bakery-card-body">
                <div class="bakery-card p-5 ring-0 bg-slate-50">
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <div class="bakery-card p-4 ring-0 bg-white">
                            <div class="text-xs font-bold text-slate-400">{{ __('page.sales') }}</div>
                            <div class="mt-2 text-xl font-extrabold text-emerald-600">Rp 72.642.450</div>
                        </div>
                        <div class="bakery-card p-4 ring-0 bg-white">
                            <div class="text-xs font-bold text-slate-400">{{ __('page.net_sales') }}</div>
                            <div class="mt-2 text-xl font-extrabold text-emerald-600">Rp 72.285.150</div>
                        </div>
                        <div class="bakery-card p-4 ring-0 bg-white">
                            <div class="text-xs font-bold text-slate-400">{{ __('page.gross_profit') }}</div>
                            <div class="mt-2 text-xl font-extrabold text-sky-600">Rp 33.736.347</div>
                        </div>
                        <div class="bakery-card p-4 ring-0 bg-white">
                            <div class="text-xs font-bold text-slate-400">{{ __('page.net_profit') }}</div>
                            <div class="mt-2 text-xl font-extrabold text-amber-600">Rp 26.658.531</div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 overflow-x-auto rounded-2xl bg-white ring-1 ring-slate-100">
                    <div class="bg-amber-50 px-5 py-4">
                        <div class="text-sm font-extrabold text-slate-800">Tandi's Bakery Homemade</div>
                        <div class="mt-1 text-xs font-bold text-slate-500">Periode 1 Juni 2025 s/d 30 Juni 2025</div>
                    </div>

                    <div class="px-5 py-5">
                        <div class="text-sm font-extrabold text-emerald-700">{{ __('page.revenue') }}</div>
                        <div class="mt-3 space-y-2 text-sm font-semibold text-slate-700">
                            <div class="flex items-center justify-between">
                                <span>{{ __('page.sales') }}</span><span>Rp 72.642.450</span>
                            </div>
                            <div class="flex items-center justify-between text-rose-600">
                                <span>{{ __('page.sales_discount') }}</span><span>(Rp 110.300)</span>
                            </div>
                            <div class="flex items-center justify-between text-rose-600">
                                <span>{{ __('page.sales_return') }}</span><span>(Rp 247.000)</span>
                            </div>
                            <div class="mt-3 h-px bg-slate-100"></div>
                            <div class="flex items-center justify-between font-extrabold text-emerald-700">
                                <span>{{ __('page.net_sales') }}</span><span>Rp 72.285.150</span>
                            </div>
                        </div>

                        <div class="mt-6 space-y-2 text-sm font-semibold text-slate-700">
                            <div class="flex items-center justify-between">
                                <span>{{ __('page.cogs') }}</span><span class="text-rose-600">(Rp 38.548.803)</span>
                            </div>
                            <div class="mt-3 h-px bg-slate-100"></div>
                            <div class="flex items-center justify-between font-extrabold text-sky-700">
                                <span>{{ __('page.gross_profit') }}</span><span>Rp 33.736.347</span>
                            </div>
                        </div>

                        <div class="mt-8 text-sm font-extrabold text-rose-700">{{ __('page.operating_expenses') }}</div>
                        <div class="mt-3 space-y-2 text-sm font-semibold text-slate-700">
                            <div class="flex items-center justify-between"><span>{{ __('page.admin_sales_salary') }}</span><span>Rp 2.000.000</span></div>
                            <div class="flex items-center justify-between"><span>{{ __('page.security_salary') }}</span><span>Rp 350.000</span></div>
                            <div class="flex items-center justify-between"><span>{{ __('page.vehicle_depreciation') }}</span><span>Rp 2.041.667</span></div>
                            <div class="flex items-center justify-between"><span>{{ __('page.insurance_cost') }}</span><span>Rp 315.549</span></div>
                            <div class="flex items-center justify-between"><span>{{ __('page.other_costs') }}</span><span>Rp 2.245.600</span></div>
                            <div class="mt-3 h-px bg-slate-100"></div>
                            <div class="flex items-center justify-between font-extrabold text-rose-700">
                                <span>{{ __('page.total_operating_expenses') }}</span><span>(Rp 6.952.816)</span>
                            </div>
                        </div>

                        <div class="mt-6 space-y-2 text-sm font-semibold text-slate-700">
                            <div class="flex items-center justify-between">
                                <span>{{ __('page.income_tax') }}</span><span class="text-rose-600">(Rp 125.000)</span>
                            </div>
                        </div>

                        <div class="mt-8 rounded-2xl bg-amber-50 px-5 py-4 ring-1 ring-amber-200/60">
                            <div class="text-sm font-extrabold text-amber-700">{{ __('page.net_profit') }}</div>
                            <div class="mt-1 text-xs font-bold text-slate-500">{{ __('page.net_profit_formula') }}</div>
                            <div class="mt-3 text-2xl font-extrabold text-amber-600">Rp 26.658.531</div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-3">
                    <div class="bakery-card p-5 ring-0 bg-slate-50">
                        <div class="text-xs font-bold text-slate-400">{{ __('page.gross_profit_margin') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-sky-600">46.7%</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-slate-50">
                        <div class="text-xs font-bold text-slate-400">{{ __('page.operating_expense_ratio') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-rose-600">9.6%</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-slate-50">
                        <div class="text-xs font-bold text-slate-400">{{ __('page.net_profit_margin') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-amber-600">36.9%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
