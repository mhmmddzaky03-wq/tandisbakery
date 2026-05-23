@extends('layouts.app')

@php
    $title = 'Product Data - Employee';
    $role = 'karyawan';
    $active = 'karyawan.produk';
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
