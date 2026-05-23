@extends('layouts.app')

@php
    $title = __('nav.sales_transactions') . ' - Admin';
    $role = 'admin';
    $active = 'admin.penjualan';
    $pageTitle = __('nav.sales_transactions');
    $subtitle = __('nav.main_menu');

    $rows = [];
    for ($i = 1; $i <= 10; $i++) {
        $rows[] = [
            'id' => 'TRX' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
            'tgl' => str_pad((string) (6 + $i), 2, '0', STR_PAD_LEFT) . ' Juni 2025',
            'total' => 'Rp ' . number_format(1800000 + ($i * 135000), 0, ',', '.'),
            'metode' => 'Mix',
            'jumlah' => (string) (15 + $i) . ' transaksi',
        ];
    }
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('page.sales_list_title') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.sales_list_subtitle') }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <a class="bakery-btn-primary" href="#" data-dummy>{{ __('page.add_transaction') }}</a>
                </div>
            </div>

            <div class="bakery-card-body">
                <div class="grid gap-4 lg:grid-cols-3">
                    <div class="bakery-card p-5 ring-0 bg-slate-50">
                        <div class="text-xs font-bold text-slate-400">{{ __('page.today_sales') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-emerald-600">Rp 0</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-slate-50">
                        <div class="text-xs font-bold text-slate-400">{{ __('page.today_transactions') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-sky-600">0</div>
                    </div>
                    <div class="bakery-card p-5 ring-0 bg-slate-50">
                        <div class="text-xs font-bold text-slate-400">{{ __('page.top_stock') }}</div>
                        <div class="mt-2 text-2xl font-extrabold text-amber-600">30</div>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl bg-slate-50 px-4 py-3 ring-1 ring-black/5">
                    <div class="flex items-center gap-2">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.2-4.2" />
                        </svg>
                        <input class="w-full bg-transparent text-sm font-semibold text-slate-700 placeholder:text-slate-400 outline-none" placeholder="{{ __('page.search_trx') }}" />
                    </div>
                </div>

                <div class="mt-5 bakery-table-wrap">
                    <table class="bakery-table">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __('page.id') }}</th>
                                <th class="text-left">{{ __('page.date') }}</th>
                                <th class="text-left">{{ __('page.total_sales') }}</th>
                                <th class="text-left">{{ __('page.payment_method') }}</th>
                                <th class="text-left">{{ __('page.transaction_count') }}</th>
                                <th class="text-left">{{ __('page.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $r)
                                <tr class="bg-white">
                                    <td class="font-bold text-slate-700">{{ $r['id'] }}</td>
                                    <td class="font-semibold text-slate-600">{{ $r['tgl'] }}</td>
                                    <td class="font-extrabold text-emerald-600">{{ $r['total'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['metode'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['jumlah'] }}</td>
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
