@extends('layouts.app')

@php
    $title = __('nav.production_data') . ' - Admin';
    $role = 'admin';
    $active = 'admin.produksi';
    $pageTitle = __('nav.production_data');
    $subtitle = __('nav.main_menu');

    $stats = [
        ['label' => __('page.total_production'), 'value' => '4', 'tone' => 'blue'],
        ['label' => __('page.production_success'), 'value' => '3', 'tone' => 'green'],
        ['label' => __('page.production_failed'), 'value' => '1', 'tone' => 'rose'],
        ['label' => __('page.success_rate'), 'value' => '75.0%', 'tone' => 'slate'],
    ];

    $rows = [
        ['id' => 'PRD001', 'tgl' => '15/4/2026', 'produk' => 'Roti Tawar', 'jumlah' => '50 loyang', 'status' => ['label' => 'Berhasil', 'tone' => 'emerald'], 'ket' => '-'],
        ['id' => 'PRD002', 'tgl' => '15/4/2026', 'produk' => 'Croissant', 'jumlah' => '100 pcs', 'status' => ['label' => 'Berhasil', 'tone' => 'emerald'], 'ket' => '-'],
        ['id' => 'PRD003', 'tgl' => '14/4/2026', 'produk' => 'Kue Ulang Tahun', 'jumlah' => '5 pcs', 'status' => ['label' => 'Berhasil', 'tone' => 'emerald'], 'ket' => '-'],
        ['id' => 'PRD004', 'tgl' => '14/4/2026', 'produk' => 'Donat', 'jumlah' => '0 pcs', 'status' => ['label' => 'Gagal', 'tone' => 'rose'], 'ket' => 'Adonan tidak mengembang sempurna'],
    ];
@endphp

@section('content')
    <div class="pt-6">
        <div class="grid gap-4 lg:grid-cols-4">
            @foreach ($stats as $s)
                <div class="bakery-card">
                    <div class="bakery-card-body pt-5">
                        <div class="text-xs font-bold text-slate-400">{{ $s['label'] }}</div>
                        <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $s['value'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('page.production_list_title') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.production_list_subtitle') }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 rounded-2xl bg-slate-50 px-4 py-2.5 ring-1 ring-black/5 w-full sm:w-auto">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.2-4.2" />
                        </svg>
                        <input class="flex-1 min-w-0 bg-transparent text-sm font-semibold text-slate-700 placeholder:text-slate-400 outline-none" placeholder="{{ __('page.search_production') }}" />
                    </div>
                    <button class="bakery-btn-primary" type="button" data-dummy>{{ __('page.add') }}</button>
                </div>
            </div>

            <div class="bakery-card-body">
                <div class="bakery-table-wrap">
                    <table class="bakery-table">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __('page.id') }}</th>
                                <th class="text-left">{{ __('page.date') }}</th>
                                <th class="text-left">{{ __('page.product_name') }}</th>
                                <th class="text-left">{{ __('page.quantity') }}</th>
                                <th class="text-left">{{ __('page.status') }}</th>
                                <th class="text-left">{{ __('page.notes') }}</th>
                                <th class="text-left">{{ __('page.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $r)
                                <tr class="bg-white">
                                    <td class="font-bold text-slate-700">{{ $r['id'] }}</td>
                                    <td class="font-semibold text-slate-600">{{ $r['tgl'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['produk'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['jumlah'] }}</td>
                                    <td>
                                        <span class="bakery-badge {{ $r['status']['tone'] === 'emerald' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                            {{ $r['status']['label'] }}
                                        </span>
                                    </td>
                                    <td class="font-semibold text-slate-500">{{ $r['ket'] }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <a class="bakery-btn-ghost px-3 py-2 text-xs font-extrabold" href="#" data-dummy>{{ __('page.detail') }}</a>
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
