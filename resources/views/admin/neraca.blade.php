@extends('layouts.app')

@php
    $title = __('nav.balance_sheet') . ' - Admin';
    $role = 'admin';
    $active = 'admin.neraca';
    $pageTitle = __('nav.balance_sheet');
    $subtitle = __('nav.financial_reports');
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('nav.balance_sheet') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.bs_subtitle') }} - 30 Juni 2025</div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="bakery-btn-ghost px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.print') }}</button>
                    <button class="bakery-btn-primary px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.download_pdf') }}</button>
                </div>
            </div>

            <div class="bakery-card-body">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="bakery-card p-5 ring-0 bg-sky-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.total_assets') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-sky-700">Rp 331.817.183</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-rose-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.total_liabilities') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-rose-700">Rp 125.000</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-emerald-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.total_equity') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-emerald-700">Rp 331.692.183</div>
                    </div>
                </div>

                <div class="mt-5 grid gap-5 lg:grid-cols-2">
                    <div class="bakery-card overflow-hidden ring-1 ring-slate-100">
                        <div class="bg-sky-50 px-5 py-4">
                            <div class="text-sm font-extrabold text-sky-700">{{ __('page.assets') }}</div>
                        </div>
                        <div class="px-5 py-5">
                            <div class="text-xs font-extrabold text-slate-500">{{ __('page.current_assets') }}</div>
                            <div class="mt-3 space-y-2 text-sm font-semibold text-slate-700">
                                <div class="flex justify-between"><span>{{ __('page.cash_in_bank') }}</span><span>Rp 20.678.197</span></div>
                                <div class="flex justify-between"><span>{{ __('page.direct_materials') }}</span><span>Rp 22.383.272</span></div>
                                <div class="flex justify-between"><span>{{ __('page.wip') }}</span><span>Rp 14.588.531</span></div>
                                <div class="flex justify-between"><span>{{ __('page.finished_goods') }}</span><span>Rp 5.727.000</span></div>
                                <div class="flex justify-between"><span>{{ __('page.factory_supplies') }}</span><span>Rp 15.000</span></div>
                                <div class="mt-3 h-px bg-slate-100"></div>
                                <div class="flex justify-between font-extrabold text-sky-700"><span>{{ __('page.total_current_assets') }}</span><span>Rp 63.391.000</span></div>
                            </div>

                            <div class="mt-7 text-xs font-extrabold text-slate-500">{{ __('page.non_current_assets') }}</div>
                            <div class="mt-3 space-y-2 text-sm font-semibold text-slate-700">
                                <div class="flex justify-between"><span>{{ __('page.factory_equipment') }}</span><span>Rp 52.967.850</span></div>
                                <div class="flex justify-between text-rose-600"><span>{{ __('page.accum_depreciation_equipment') }}</span><span>(Rp 27.500.000)</span></div>
                                <div class="flex justify-between"><span>{{ __('page.book_value_equipment') }}</span><span>Rp 25.467.850</span></div>
                                <div class="flex justify-between"><span>{{ __('page.vehicle') }}</span><span>Rp 245.000.000</span></div>
                                <div class="flex justify-between text-rose-600"><span>{{ __('page.accum_depreciation_vehicle') }}</span><span>(Rp 2.041.667)</span></div>
                                <div class="flex justify-between"><span>{{ __('page.book_value_vehicle') }}</span><span>Rp 242.958.333</span></div>
                                <div class="mt-3 h-px bg-slate-100"></div>
                                <div class="flex justify-between font-extrabold text-sky-700"><span>{{ __('page.total_non_current_assets') }}</span><span>Rp 268.426.183</span></div>
                            </div>

                            <div class="mt-7 rounded-2xl bg-sky-50 px-5 py-4">
                                <div class="flex items-center justify-between font-extrabold text-sky-700">
                                    <span>{{ __('page.total_assets_label') }}</span><span>Rp 331.817.183</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="bakery-card overflow-hidden ring-1 ring-slate-100">
                            <div class="bg-rose-50 px-5 py-4">
                                <div class="text-sm font-extrabold text-rose-700">{{ __('page.liabilities') }}</div>
                            </div>
                            <div class="px-5 py-5">
                                <div class="text-xs font-extrabold text-slate-500">{{ __('page.current_liabilities') }}</div>
                                <div class="mt-3 space-y-2 text-sm font-semibold text-slate-700">
                                    <div class="flex justify-between"><span>{{ __('page.tax_payable') }}</span><span>Rp 125.000</span></div>
                                    <div class="mt-3 h-px bg-slate-100"></div>
                                    <div class="flex justify-between font-extrabold text-rose-700"><span>{{ __('page.total_liabilities_label') }}</span><span>Rp 125.000</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="bakery-card overflow-hidden ring-1 ring-slate-100">
                            <div class="bg-emerald-50 px-5 py-4">
                                <div class="text-sm font-extrabold text-emerald-700">{{ __('page.equity') }}</div>
                            </div>
                            <div class="px-5 py-5">
                                <div class="mt-1 space-y-2 text-sm font-semibold text-slate-700">
                                    <div class="flex justify-between"><span>{{ __('page.owner_capital') }}</span><span>Rp 276.668.675</span></div>
                                    <div class="flex justify-between"><span>{{ __('page.retained_earnings') }}</span><span>Rp 29.158.652</span></div>
                                    <div class="flex justify-between"><span>{{ __('page.net_profit_current') }}</span><span>Rp 26.658.531</span></div>
                                    <div class="mt-3 h-px bg-slate-100"></div>
                                    <div class="flex justify-between font-extrabold text-emerald-700"><span>{{ __('page.total_equity_label') }}</span><span>Rp 331.692.183</span></div>
                                </div>

                                <div class="mt-6 rounded-2xl bg-emerald-50 px-5 py-4">
                                    <div class="flex items-center justify-between font-extrabold text-emerald-700">
                                        <span>{{ __('page.total_liabilities_equity') }}</span><span>Rp 331.817.183</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
