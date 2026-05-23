@extends('layouts.app')

@php
    $title = 'General Ledger - Admin';
    $role = 'admin';
    $active = 'admin.gl';
    $pageTitle = 'General Ledger';
    $subtitle = __('nav.accounting');

    $rows = [
        ['tgl' => '30 Jun', 'ref' => 'Bayar Gaji', 'debit' => 'Rp 10.300.000', 'kredit' => '-', 'saldo' => 'Rp 10.300.000'],
        ['tgl' => '30 Jun', 'ref' => 'ALLOCATING GAJI', 'debit' => '-', 'kredit' => 'Rp 10.300.000', 'saldo' => 'Rp 0'],
    ];
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('nav.general_ledger') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.gl_subtitle') }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="bakery-btn-ghost px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.print') }}</button>
                    <button class="bakery-btn-primary px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.download_pdf') }}</button>
                </div>
            </div>

            <div class="bakery-card-body">
                <div class="bakery-card p-5 ring-0 bg-slate-50">
                    <div class="grid gap-4 lg:grid-cols-3 lg:items-center">
                        <div class="lg:col-span-2">
                            <div class="text-xs font-bold text-slate-500 mb-2">{{ __('page.select_account') }}</div>
                            <div class="rounded-2xl bg-white px-4 py-3 ring-1 ring-black/10">
                                <div class="text-sm font-semibold text-slate-700">5-150 — Salary Expense</div>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-500 mb-2">{{ __('page.account_info') }}</div>
                            <div class="rounded-2xl bg-white px-4 py-3 ring-1 ring-black/10">
                                <div class="text-xs font-bold text-slate-400">{{ __('page.position') }}: <span class="text-sky-600">Dr</span></div>
                                <div class="mt-1 text-xs font-bold text-slate-400">{{ __('page.group') }}: <span class="text-slate-700">Expenses</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 overflow-x-auto rounded-2xl bg-white ring-1 ring-slate-100">
                    <div class="bg-amber-50 px-5 py-4">
                        <div class="text-sm font-extrabold text-slate-800">Salary Expense</div>
                        <div class="mt-1 text-xs font-bold text-slate-500">5-150 — Dr</div>
                    </div>

                    <table class="bakery-table">
                        <thead>
                            <tr>
                                <th class="text-left">No</th>
                                <th class="text-left">{{ __('page.date') }}</th>
                                <th class="text-left">Ref</th>
                                <th class="text-left">{{ __('page.debit') }}</th>
                                <th class="text-left">{{ __('page.credit') }}</th>
                                <th class="text-left">{{ __('page.balance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white">
                                <td class="font-bold text-slate-700">-</td>
                                <td class="font-semibold text-slate-600">1-Jun</td>
                                <td class="font-semibold text-slate-600">BEG. BALANCE</td>
                                <td class="font-semibold text-slate-700">-</td>
                                <td class="font-semibold text-slate-700">-</td>
                                <td class="font-extrabold text-slate-900">Rp 0</td>
                            </tr>
                            @foreach ($rows as $i => $r)
                                <tr class="bg-white">
                                    <td class="font-bold text-slate-700">{{ $i + 1 }}</td>
                                    <td class="font-semibold text-slate-600">{{ $r['tgl'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['ref'] }}</td>
                                    <td class="font-semibold text-sky-600">{{ $r['debit'] }}</td>
                                    <td class="font-semibold text-emerald-600">{{ $r['kredit'] }}</td>
                                    <td class="font-extrabold text-slate-900">{{ $r['saldo'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-4">
                    <div class="bakery-card p-5 ring-0 bg-slate-50">
                        <div class="text-xs font-bold text-slate-400">{{ __('page.total_transactions') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-slate-900">2</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-sky-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.total_debit') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-sky-700">Rp 10.300.000</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-emerald-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.total_credit') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-emerald-700">Rp 10.300.000</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-amber-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.ending_balance') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-amber-700">Rp 0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
