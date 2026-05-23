@extends('layouts.app')

@php
    $title = 'Journal Entries - Admin';
    $role = 'admin';
    $active = 'admin.jurnal';
    $pageTitle = __('nav.journal_entries');
    $subtitle = __('nav.accounting');

    // Sample journal entries grouped by date (matching screenshot structure)
    $journals = [
        [
            'tanggal' => '1 Juni 2025',
            'hari' => 'Minggu',
            'entries' => [
                ['ref' => 'Bisa bersong candle.co.id', 'akun' => '1-110', 'uraian' => 'Mekonet Donas', 'debit' => '', 'kredit' => ''],
                ['ref' => 'Restock bahan baku', 'akun' => '1-130', 'uraian' => 'Yutimiki Trading', 'debit' => '', 'kredit' => ''],
                ['ref' => 'Investasi atp (During reports exam)', 'akun' => '1-110', 'uraian' => 'Per (Equipment)', 'debit' => '', 'kredit' => 'Rp 1.097.850'],
                ['ref' => 'Investasi atp Ganjing Reports bruto', 'akun' => '3-110', 'uraian' => 'Ganjal Tandi\'s', 'debit' => '', 'kredit' => ''],
                ['ref' => 'Moeder verzintarien', 'akun' => '1-130', 'uraian' => 'Yutimiki', 'debit' => '', 'kredit' => ''],
                ['ref' => 'Gas prices', 'akun' => '1-130', 'uraian' => 'Other industry', 'debit' => '', 'kredit' => ''],
                ['ref' => 'Moeder Mooer', 'akun' => '1-110', 'uraian' => 'Per (Equipment)', 'debit' => 'Rp 28.891.200', 'kredit' => ''],
                ['ref' => 'Moeder Mooer', 'akun' => '3-110', 'uraian' => 'Ganjal Tandi\'s', 'debit' => '', 'kredit' => ''],
            ],
            'total_debit' => 'Rp 139.411.375',
            'total_kredit' => 'Rp 287.112.925',
        ],
        [
            'tanggal' => '5 Juni 2025',
            'hari' => 'Kamis',
            'entries' => [
                ['ref' => '', 'akun' => '1-110', 'uraian' => 'Cash in Tandi\'s Bank', 'debit' => '', 'kredit' => ''],
                ['ref' => 'Ponos Tradisional\u003C', 'akun' => '1-110', 'uraian' => 'Cash in Tandi\'s Bank', 'debit' => '', 'kredit' => ''],
                ['ref' => 'Ponos Tradisional', 'akun' => '1-110', 'uraian' => 'Cash in Tandi\'s Bank', 'debit' => '', 'kredit' => ''],
                ['ref' => 'Bevelen Mandiri', 'akun' => '1-110', 'uraian' => 'Pendekan of Heart & Henry', 'debit' => 'Rp 137.000', 'kredit' => ''],
                ['ref' => 'Bevelen Mandiri', 'akun' => '1-110', 'uraian' => 'Cash in Tandi\'s Bank', 'debit' => '', 'kredit' => ''],
            ],
            'total_debit' => 'Rp 2.297.860',
            'total_kredit' => 'Rp 2.297.860',
        ],
        [
            'tanggal' => '3 Juni 2025',
            'hari' => 'Selasa',
            'entries' => [
                ['ref' => '', 'akun' => '1-110', 'uraian' => '', 'debit' => 'Rp 17.232.150', 'kredit' => ''],
                ['ref' => 'PT Merging Nusantara Investation', 'akun' => '1-110', 'uraian' => 'Pacifist of Wheldrake Pacific', 'debit' => 'Rp 19.914', 'kredit' => ''],
                ['ref' => 'PT Merium kredit Yulianti sen', 'akun' => '1-110', 'uraian' => 'Cash in Tandi\'s Bank', 'debit' => '', 'kredit' => 'Rp 180.523'],
            ],
            'total_debit' => '',
            'total_kredit' => 'Rp 874.100',
        ],
        [
            'tanggal' => '4 Juni 2025',
            'hari' => 'Rabu',
            'entries' => [
                ['ref' => '', 'akun' => '1-110', 'uraian' => 'Cash in Tan Bulbar', 'debit' => 'Rp 184.873', 'kredit' => ''],
                ['ref' => '', 'akun' => '4-110', 'uraian' => 'Sales', 'debit' => '', 'kredit' => ''],
                ['ref' => 'Bevelen Mandiri', 'akun' => '2-111', 'uraian' => 'Pendekan of Heart & Henry', 'debit' => 'Rp 184.873', 'kredit' => ''],
                ['ref' => 'Lamaran termasuk', 'akun' => '1-130', 'uraian' => 'Limiten verbintency', 'debit' => '', 'kredit' => ''],
                ['ref' => 'Tanda', 'akun' => '1-110', 'uraian' => '', 'debit' => '', 'kredit' => ''],
                ['ref' => 'Tanda', 'akun' => '1-110', 'uraian' => 'Cash in TandiBulbar', 'debit' => '', 'kredit' => ''],
            ],
            'total_debit' => '',
            'total_kredit' => '',
        ],
    ];

    // Summary totals
    $totalTransaksi = 224;
    $totalDebit = 'Rp 544,139,906';
    $totalKredit = 'Rp 544,139,906';
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('nav.journal_entries') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.journal_subtitle') }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="bakery-btn-ghost px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.print') }}</button>
                    <button class="bakery-btn-primary px-4 py-2.5 text-sm font-extrabold" data-dummy>{{ __('page.add_entry') }}</button>
                </div>
            </div>

            <div class="bakery-card-body">
                {{-- Filters --}}
                <div class="bakery-card p-5 ring-0 bg-slate-50">
                    <div class="grid gap-4 lg:grid-cols-2 lg:items-center">
                        <div>
                            <div class="text-xs font-bold text-slate-500 mb-2">{{ __('page.search_transaction') }}</div>
                            <div class="rounded-2xl bg-white px-4 py-3 ring-1 ring-black/10">
                                <input class="w-full bg-transparent text-sm font-semibold text-slate-700 placeholder:text-slate-400 outline-none" placeholder="{{ __('page.search_by_ref_account') }}" />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-500 mb-2">{{ __('page.filter_date') }}</div>
                            <div class="rounded-2xl bg-white px-4 py-3 ring-1 ring-black/10">
                                <div class="text-sm font-semibold text-slate-700">{{ __('page.add_entry_hint') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Journal Entries grouped by date --}}
                @foreach ($journals as $journal)
                    <div class="mt-5 overflow-x-auto rounded-2xl bg-white ring-1 ring-slate-100">
                        <div class="bg-amber-50 px-5 py-3 flex items-center justify-between">
                            <div class="text-sm font-extrabold text-slate-800">{{ $journal['hari'] }}, {{ $journal['tanggal'] }}</div>
                            <div class="text-xs font-bold text-slate-400">{{ count($journal['entries']) }} {{ __('page.entries') }}</div>
                        </div>

                        <table class="bakery-table">
                            <thead>
                                <tr>
                                    <th class="text-left">Ref</th>
                                    <th class="text-left">{{ __('page.account_code') }}</th>
                                    <th class="text-left">{{ __('page.description') }}</th>
                                    <th class="text-left">{{ __('page.debit') }}</th>
                                    <th class="text-left">{{ __('page.credit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($journal['entries'] as $e)
                                    <tr class="bg-white">
                                        <td class="font-semibold text-slate-600">{{ $e['ref'] ?: '-' }}</td>
                                        <td class="font-bold text-slate-700">{{ $e['akun'] }}</td>
                                        <td class="font-semibold text-slate-600">{{ $e['uraian'] ?: '-' }}</td>
                                        <td class="font-semibold text-sky-600">{{ $e['debit'] ?: '-' }}</td>
                                        <td class="font-semibold text-emerald-600">{{ $e['kredit'] ?: '-' }}</td>
                                    </tr>
                                @endforeach
                                @if ($journal['total_debit'] || $journal['total_kredit'])
                                    <tr class="bg-amber-50/60">
                                        <td colspan="3" class="font-extrabold text-slate-700 text-right">Total</td>
                                        <td class="font-extrabold text-sky-700">{{ $journal['total_debit'] ?: '-' }}</td>
                                        <td class="font-extrabold text-emerald-700">{{ $journal['total_kredit'] ?: '-' }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endforeach

                {{-- Summary --}}
                <div class="mt-5 grid gap-4 md:grid-cols-3">
                    <div class="bakery-card p-5 ring-0 bg-slate-50">
                        <div class="text-xs font-bold text-slate-400">{{ __('page.total_transactions') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ $totalTransaksi }}</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-sky-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.total_debit') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-sky-700">{{ $totalDebit }}</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-emerald-50">
                        <div class="text-xs font-bold text-slate-500">{{ __('page.total_credit') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-emerald-700">{{ $totalKredit }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection