@extends('layouts.app')

@php
    $title = __('nav.operational_costs') . ' - Admin';
    $role = 'admin';
    $active = 'admin.operasional';
    $pageTitle = __('nav.operational_costs');
    $subtitle = __('nav.main_menu');

    $rows = [
        ['id' => 'BO001', 'tgl' => '1/6/2025', 'kat' => 'Kemasan', 'desk' => 'Pasar Tradisional - Bahan kemasan dan kebutuhan operasional', 'jumlah' => 'Rp 420.000', 'jenis' => ['label' => __('page.variable_cost'), 'tone' => 'amber']],
        ['id' => 'BO002', 'tgl' => '30/6/2025', 'kat' => 'Air', 'desk' => 'Refill Cleo - Air minum untuk operasional', 'jumlah' => 'Rp 167.000', 'jenis' => ['label' => __('page.variable_cost'), 'tone' => 'amber']],
        ['id' => 'BO008', 'tgl' => '10/6/2025', 'kat' => 'Lainnya', 'desk' => 'Cimbniaga Cicilan - Bayar HP Iphone (cicilan)', 'jumlah' => 'Rp 2.041.500', 'jenis' => ['label' => __('page.fixed_cost'), 'tone' => 'sky']],
        ['id' => 'BO014', 'tgl' => '30/6/2025', 'kat' => 'Lainnya', 'desk' => 'Online - Pembelian kebutuhan online', 'jumlah' => 'Rp 1.000.000', 'jenis' => ['label' => __('page.variable_cost'), 'tone' => 'amber']],
    ];
@endphp

@section('content')
    <div class="pt-6">
        <div class="grid gap-4 lg:grid-cols-3">
            <div class="bakery-card">
                <div class="bakery-card-body pt-5">
                    <div class="text-xs font-bold text-slate-400">{{ __('page.total_cost') }}</div>
                    <div class="mt-2 text-2xl font-extrabold text-slate-900">Rp 4.941.500</div>
                </div>
            </div>
            <div class="bakery-card">
                <div class="bakery-card-body pt-5">
                    <div class="text-xs font-bold text-slate-400">{{ __('page.fixed_cost_total') }}</div>
                    <div class="mt-2 text-2xl font-extrabold text-sky-600">Rp 2.041.500</div>
                </div>
            </div>
            <div class="bakery-card">
                <div class="bakery-card-body pt-5">
                    <div class="text-xs font-bold text-slate-400">{{ __('page.variable_cost_total') }}</div>
                    <div class="mt-2 text-2xl font-extrabold text-amber-600">Rp 2.900.000</div>
                </div>
            </div>
        </div>

        <div class="mt-5 bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('page.cost_list_title') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.cost_list_subtitle') }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <a class="bakery-btn-primary" href="#" data-dummy>{{ __('page.add_cost') }}</a>
                </div>
            </div>

            <div class="bakery-card-body">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2 rounded-2xl bg-slate-100 p-1.5">
                        <button class="rounded-xl bg-white px-4 py-2 text-xs font-extrabold text-slate-900 shadow-sm">{{ __('page.all') }}</button>
                        <button class="rounded-xl px-4 py-2 text-xs font-extrabold text-slate-500">{{ __('page.fixed_cost') }}</button>
                        <button class="rounded-xl px-4 py-2 text-xs font-extrabold text-slate-500">{{ __('page.variable_cost') }}</button>
                    </div>

                    <div class="flex items-center gap-2 rounded-2xl bg-slate-50 px-4 py-2.5 ring-1 ring-black/5 w-full sm:w-auto">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.2-4.2" />
                        </svg>
                        <input class="flex-1 min-w-0 bg-transparent text-sm font-semibold text-slate-700 placeholder:text-slate-400 outline-none" placeholder="{{ __('page.search_cost') }}" />
                    </div>
                </div>

                <div class="mt-5 bakery-table-wrap">
                    <table class="bakery-table">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __('page.id') }}</th>
                                <th class="text-left">{{ __('page.date') }}</th>
                                <th class="text-left">{{ __('page.category') }}</th>
                                <th class="text-left">{{ __('page.description') }}</th>
                                <th class="text-left">{{ __('page.amount') }}</th>
                                <th class="text-left">{{ __('page.cost_type') }}</th>
                                <th class="text-left">{{ __('page.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $r)
                                <tr class="bg-white">
                                    <td class="font-bold text-slate-700">{{ $r['id'] }}</td>
                                    <td class="font-semibold text-slate-600">{{ $r['tgl'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['kat'] }}</td>
                                    <td class="font-semibold text-slate-500">{{ $r['desk'] }}</td>
                                    <td class="font-extrabold text-rose-600">{{ $r['jumlah'] }}</td>
                                    <td>
                                        <span class="bakery-badge {{ $r['jenis']['tone'] === 'sky' ? 'bg-sky-50 text-sky-600' : 'bg-amber-50 text-amber-700' }}">
                                            {{ $r['jenis']['label'] }}
                                        </span>
                                    </td>
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
