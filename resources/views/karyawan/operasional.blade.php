@extends('layouts.app')

@php
    $title = 'Operational Input - Employee';
    $role = 'karyawan';
    $active = 'karyawan.operasional';
    $pageTitle = __('nav.input_operational');
    $subtitle = __('nav.main_menu');

    $rows = [
        ['id' => 'BO001', 'tgl' => '1/6/2025', 'kat' => 'Kemasan', 'desk' => 'Pasar Tradisional - Bahan kemasan dan kebutuhan operasional', 'jumlah' => 'Rp 420.000', 'jenis' => ['label' => __('page.variable_cost'), 'tone' => 'amber']],
        ['id' => 'BO008', 'tgl' => '10/6/2025', 'kat' => 'Lainnya', 'desk' => 'Cimbniaga Cicilan - Bayar HP Iphone (cicilan)', 'jumlah' => 'Rp 2.041.500', 'jenis' => ['label' => __('page.fixed_cost'), 'tone' => 'sky']],
    ];
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
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
                <div class="bakery-table-wrap">
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
