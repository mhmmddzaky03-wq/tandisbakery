@extends('layouts.app')

@php
    $title = __('nav.trial_balance') . ' - Admin';
    $role = 'admin';
    $active = 'admin.tb';
    $pageTitle = __('nav.trial_balance');
    $subtitle = __('nav.accounting');

    $rows = [
        ['kode' => '1-110', 'nama' => 'Cash in Tandi\'s Bank', 'debit' => 'Rp 20.678.197', 'kredit' => '-'],
        ['kode' => '1-130', 'nama' => 'Direct Materials', 'debit' => 'Rp 22.383.272', 'kredit' => '-'],
        ['kode' => '1-140', 'nama' => 'Work in Process', 'debit' => 'Rp 14.588.531', 'kredit' => '-'],
        ['kode' => '2-140', 'nama' => 'IPHONE Payable', 'debit' => '-', 'kredit' => 'Rp 2.401.000'],
        ['kode' => '4-110', 'nama' => 'Sales', 'debit' => '-', 'kredit' => 'Rp 72.285.150'],
        ['kode' => '5-150', 'nama' => 'Salary Expense', 'debit' => 'Rp 10.300.000', 'kredit' => '-'],
    ];
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('nav.trial_balance') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.tb_subtitle') }} - 30 Juni 2025</div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="bakery-btn-ghost px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.print') }}</button>
                    <button class="bakery-btn-primary px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.download_pdf') }}</button>
                </div>
            </div>

            <div class="bakery-card-body">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="bakery-card p-5 ring-0 bg-sky-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.total_debit') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-sky-700">Rp 419.768.657</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-emerald-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.total_credit') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-emerald-700">Rp 419.768.657</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-slate-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.status') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-emerald-600">{{ __('page.balanced') }}</div>
                        <div class="mt-1 text-xs font-bold text-slate-400">{{ __('page.difference') }}: Rp 0</div>
                    </div>
                </div>

                <div class="mt-5 bakery-table-wrap">
                    <table class="bakery-table">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __('page.account_code') }}</th>
                                <th class="text-left">{{ __('page.account_name') }}</th>
                                <th class="text-left">{{ __('page.debit') }}</th>
                                <th class="text-left">{{ __('page.credit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $r)
                                <tr class="bg-white">
                                    <td class="font-bold text-slate-700">{{ $r['kode'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['nama'] }}</td>
                                    <td class="font-semibold text-sky-700">{{ $r['debit'] }}</td>
                                    <td class="font-semibold text-emerald-700">{{ $r['kredit'] }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-amber-50">
                                <td colspan="2" class="font-extrabold text-slate-800">{{ __('page.total') }}</td>
                                <td class="font-extrabold text-sky-700">Rp 419.768.657</td>
                                <td class="font-extrabold text-emerald-700">Rp 419.768.657</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
