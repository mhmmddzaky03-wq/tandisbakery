@extends('layouts.app')

@php
    $title = 'Chart of Accounts - Admin';
    $role = 'admin';
    $active = 'admin.coa';
    $pageTitle = __('nav.coa');
    $subtitle = __('nav.accounting');

    $rows = [
        ['kode' => '1-110', 'nama' => 'Cash in Tandi\'s Bank', 'posisi' => 'Debit', 'grup' => 'Current Asset', 'saldo' => 'Rp 20.678.197'],
        ['kode' => '1-130', 'nama' => 'Direct Materials', 'posisi' => 'Debit', 'grup' => 'Current Asset', 'saldo' => 'Rp 22.383.272'],
        ['kode' => '4-110', 'nama' => 'Sales', 'posisi' => 'Credit', 'grup' => 'Revenue', 'saldo' => 'Rp 72.285.150'],
        ['kode' => '5-150', 'nama' => 'Salary Expense', 'posisi' => 'Debit', 'grup' => 'Expenses', 'saldo' => 'Rp 10.300.000'],
    ];
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('nav.coa') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.coa_subtitle') }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="bakery-btn-ghost px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.print') }}</button>
                    <button class="bakery-btn-primary px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.download_pdf') }}</button>
                </div>
            </div>

            <div class="bakery-card-body">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-black/5 w-full">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.2-4.2" />
                            </svg>
                            <input class="w-full bg-transparent text-sm font-semibold text-slate-700 placeholder:text-slate-400 outline-none" placeholder="{{ __('page.search_account') }}" />
                        </div>
                    </div>
                </div>

                <div class="mt-5 bakery-table-wrap">
                    <table class="bakery-table">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __('page.account_code') }}</th>
                                <th class="text-left">{{ __('page.account_name') }}</th>
                                <th class="text-left">{{ __('page.position') }}</th>
                                <th class="text-left">{{ __('page.group') }}</th>
                                <th class="text-left">{{ __('page.balance') }}</th>
                                <th class="text-left">{{ __('page.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $r)
                                <tr class="bg-white">
                                    <td class="font-bold text-slate-700">{{ $r['kode'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['nama'] }}</td>
                                    <td class="font-semibold text-slate-600">{{ $r['posisi'] }}</td>
                                    <td class="font-semibold text-slate-600">{{ $r['grup'] }}</td>
                                    <td class="font-extrabold text-slate-900">{{ $r['saldo'] }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <button class="grid h-9 w-9 place-items-center rounded-xl bg-slate-50 ring-1 ring-black/5 hover:bg-slate-100" type="button" aria-label="Edit" data-dummy>
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                                </svg>
                                            </button>
                                            <button class="grid h-9 w-9 place-items-center rounded-xl bg-rose-50 ring-1 ring-rose-200/60 hover:bg-rose-100" type="button" aria-label="Hapus" data-dummy>
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4h8v2" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14H6L5 6" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection