@extends('layouts.app')

@php
    $title = __('nav.sales_report') . ' - Admin';
    $role = 'admin';
    $active = 'admin.laporan_penjualan';
    $pageTitle = __('nav.sales_report');
    $subtitle = __('nav.financial_reports');
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('nav.sales_report') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.sales_report_subtitle') }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="bakery-btn-ghost px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.print') }}</button>
                    <button class="bakery-btn-primary px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.download_pdf') }}</button>
                </div>
            </div>

            <div class="bakery-card-body">
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="bakery-card p-5 ring-0 bg-emerald-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.total_sales') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-emerald-700">Rp 72.642.450</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-amber-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.transactions') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-amber-700">328</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-sky-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.avg_per_day') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-sky-700">Rp 2.421.415</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-slate-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.best_selling') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-slate-900">Roti Tawar</div>
                    </div>
                </div>

                <div class="mt-5 bakery-table-wrap">
                    <table class="bakery-table">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __('page.date') }}</th>
                                <th class="text-left">{{ __('page.total_sales') }}</th>
                                <th class="text-left">{{ __('page.transactions') }}</th>
                                <th class="text-left">{{ __('page.method') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($d = 1; $d <= 7; $d++)
                                <tr class="bg-white">
                                    <td class="font-semibold text-slate-600">{{ str_pad((string) $d, 2, '0', STR_PAD_LEFT) }}/06/2025</td>
                                    <td class="font-extrabold text-emerald-600">Rp {{ number_format(2000000 + ($d * 325000), 0, ',', '.') }}</td>
                                    <td class="font-semibold text-slate-700">{{ 18 + $d }} transaksi</td>
                                    <td class="font-semibold text-slate-700">Mix</td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
