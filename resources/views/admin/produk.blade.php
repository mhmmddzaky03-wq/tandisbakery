@extends('layouts.app')

@php
    $title = __('nav.product_data') . ' - Admin';
    $role = 'admin';
    $active = 'admin.produk';
    $pageTitle = __('nav.product_data');
    $subtitle = __('nav.main_menu');

    $rows = [
        ['id' => 'P001', 'nama' => 'Roti Tawar', 'satuan' => 'loyang', 'harga' => 'Rp 25.000', 'status' => 'Aktif'],
        ['id' => 'P002', 'nama' => 'Croissant', 'satuan' => 'pcs', 'harga' => 'Rp 12.000', 'status' => 'Aktif'],
        ['id' => 'P003', 'nama' => 'Kue Ulang Tahun', 'satuan' => 'pcs', 'harga' => 'Rp 350.000', 'status' => 'Aktif'],
    ];
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('page.product_list_title') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.product_list_subtitle') }}</div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="bakery-btn-primary" data-dummy>{{ __('page.add_product') }}</button>
                </div>
            </div>

            <div class="bakery-card-body">
                <div class="bakery-table-wrap">
                    <table class="bakery-table">
                        <thead>
                            <tr>
                                <th class="text-left">{{ __('page.id') }}</th>
                                <th class="text-left">{{ __('page.product_name') }}</th>
                                <th class="text-left">{{ __('page.unit') }}</th>
                                <th class="text-left">{{ __('page.price') }}</th>
                                <th class="text-left">{{ __('page.status') }}</th>
                                <th class="text-left">{{ __('page.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $r)
                                <tr class="bg-white">
                                    <td class="font-bold text-slate-700">{{ $r['id'] }}</td>
                                    <td class="font-semibold text-slate-700">{{ $r['nama'] }}</td>
                                    <td class="font-semibold text-slate-600">{{ $r['satuan'] }}</td>
                                    <td class="font-extrabold text-amber-600">{{ $r['harga'] }}</td>
                                    <td><span class="bakery-badge bg-emerald-50 text-emerald-600">{{ $r['status'] }}</span></td>
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
