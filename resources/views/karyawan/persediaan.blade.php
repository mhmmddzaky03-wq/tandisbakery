@extends('layouts.app')

@php
    $title = 'Inventory Input - Employee';
    $role = 'karyawan';
    $active = 'karyawan.persediaan';
    $pageTitle = __('nav.input_inventory');
    $subtitle = __('nav.main_menu');

    $rows = [
        ['id' => 'SBB001', 'nama' => 'Wincheez Custom (B) 8 x 2 kg', 'jumlah' => '48 kg', 'min' => '16 kg', 'harga' => 'Rp 54.323', 'nilai' => 'Rp 2.607.504', 'status' => ['label' => 'Stok Aman', 'tone' => 'emerald'], 'update' => '21/6/2025'],
        ['id' => 'SBB006', 'nama' => 'UHT Milk Full Cream Sleeve 1kg', 'jumlah' => '1 kg', 'min' => '1 kg', 'harga' => 'Rp 176.904', 'nilai' => 'Rp 176.904', 'status' => ['label' => 'Perlu Diisi', 'tone' => 'amber'], 'update' => '18/6/2025'],
        ['id' => 'SBB010', 'nama' => 'Kismis Hitam 1kg USA Premium', 'jumlah' => '1 kg', 'min' => '1 kg', 'harga' => 'Rp 50.000', 'nilai' => 'Rp 50.000', 'status' => ['label' => 'Perlu Diisi', 'tone' => 'amber'], 'update' => '13/6/2025'],
    ];
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('page.stock_list_title') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.stock_list_subtitle') }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 rounded-2xl bg-slate-50 px-4 py-2.5 ring-1 ring-black/5 w-full sm:w-auto">
                        <svg viewBox="0 0 24 24" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.2-4.2" />
                        </svg>
                        <input class="flex-1 min-w-0 bg-transparent text-sm font-semibold text-slate-700 placeholder:text-slate-400 outline-none" placeholder="{{ __('page.search_stock') }}" />
                    </div>
                </div>
            </div>

            <div class="bakery-card-body">
                <div class="bakery-table-wrap">
                    <table class="bakery-table">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __('page.id') }}</th>
                                <th class="text-left">{{ __('page.name') }}</th>
                                <th class="text-left">{{ __('page.quantity') }}</th>
                                <th class="text-left">{{ __('page.min_threshold') }}</th>
                                <th class="text-left">{{ __('page.unit_price') }}</th>
                                <th class="text-left">{{ __('page.total_value') }}</th>
                                <th class="text-left">{{ __('page.status') }}</th>
                                <th class="text-left">{{ __('page.last_update') }}</th>
                                <th class="text-left">{{ __('page.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $r)
                                <tr class="bg-white">
                                    <td class="font-bold text-slate-700">{{ $r['id'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['nama'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['jumlah'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['min'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['harga'] }}</td>
                                    <td class="font-extrabold text-amber-600">{{ $r['nilai'] }}</td>
                                    <td>
                                        <span class="bakery-badge {{ $r['status']['tone'] === 'emerald' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-700' }}">
                                            {{ $r['status']['label'] }}
                                        </span>
                                    </td>
                                    <td class="font-semibold text-slate-500">{{ $r['update'] }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <button class="grid h-9 w-9 place-items-center rounded-xl bg-slate-50 ring-1 ring-black/5 hover:bg-slate-100" type="button" aria-label="Edit" data-dummy>
                                                <svg viewBox="0 0 24 24" class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                                </svg>
                                            </button>
                                            <button class="grid h-9 w-9 place-items-center rounded-xl bg-rose-50 ring-1 ring-rose-200/60 hover:bg-rose-100" type="button" aria-label="Delete" data-dummy>
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
