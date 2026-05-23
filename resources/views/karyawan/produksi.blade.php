@extends('layouts.app')

@php
    $title = 'Production Input - Employee';
    $role = 'karyawan';
    $active = 'karyawan.produksi';
    $pageTitle = __('nav.input_production');
    $subtitle = __('nav.main_menu');

    $rows = [
        ['id' => 'PRD001', 'tgl' => '15/4/2026', 'produk' => 'Roti Tawar', 'jumlah' => '50 loyang', 'status' => 'Berhasil'],
        ['id' => 'PRD002', 'tgl' => '15/4/2026', 'produk' => 'Croissant', 'jumlah' => '100 pcs', 'status' => 'Berhasil'],
        ['id' => 'PRD003', 'tgl' => '14/4/2026', 'produk' => 'Kue Ulang Tahun', 'jumlah' => '5 pcs', 'status' => 'Berhasil'],
        ['id' => 'PRD004', 'tgl' => '14/4/2026', 'produk' => 'Donat', 'jumlah' => '0 pcs', 'status' => 'Gagal'],
    ];
@endphp

@section('content')
    <div class="pt-6">
        <div class="bakery-card">
            <div class="bakery-card-header">
                <div>
                    <div class="text-lg font-extrabold text-slate-900">{{ __('page.production_list_title') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-400">{{ __('page.production_list_subtitle') }}</div>
                </div>
                <button class="bakery-btn-primary" type="button" data-modal-open="produksi-baru">{{ __('page.add') }} {{ __('nav.input_production') }}</button>
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
                                        <span class="bakery-badge {{ $r['status'] === 'Berhasil' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">{{ $r['status'] }}</span>
                                    </td>
                                    <td>
                                        <a class="bakery-btn-ghost px-3 py-2 text-xs font-extrabold" href="#" data-dummy>{{ __('page.detail') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <dialog data-modal="produksi-baru" class="mx-auto my-auto w-[560px] max-w-[92vw] rounded-2xl p-0 backdrop:bg-black/30">
            <div class="bakery-card !rounded-2xl ring-0 shadow-none">
                <div class="bakery-card-header">
                    <div class="text-base font-extrabold text-slate-900">Input {{ __('nav.input_production') }}</div>
                    <button type="button" class="grid h-9 w-9 place-items-center rounded-xl bg-slate-50 ring-1 ring-black/5" data-modal-close aria-label="Close">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>
                <div class="bakery-card-body">
                    <div class="space-y-4">
                        <div>
                            <div class="mb-2 text-xs font-bold text-slate-500">{{ __('page.date') }}</div>
                            <input class="bakery-input" type="date" />
                        </div>
                        <div>
                            <div class="mb-2 text-xs font-bold text-slate-500">{{ __('page.product_name') }}</div>
                            <input class="bakery-input" placeholder="Ex: Roti Tawar" />
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <div class="mb-2 text-xs font-bold text-slate-500">{{ __('page.quantity') }}</div>
                                <input class="bakery-input" placeholder="50" />
                            </div>
                            <div>
                                <div class="mb-2 text-xs font-bold text-slate-500">{{ __('page.unit') }}</div>
                                <input class="bakery-input" placeholder="loyang" />
                            </div>
                        </div>
                        <div>
                            <div class="mb-2 text-xs font-bold text-slate-500">{{ __('page.status') }}</div>
                            <input class="bakery-input" placeholder="Berhasil" />
                        </div>
                        <div>
                            <div class="mb-2 text-xs font-bold text-slate-500">{{ __('page.notes') }} (Optional)</div>
                            <input class="bakery-input" placeholder="Additional notes..." />
                        </div>

                        <button class="bakery-btn-primary w-full" data-dummy>Save Data</button>
                        <button type="button" class="w-full text-center text-sm font-extrabold text-slate-700" data-modal-close>Cancel</button>
                    </div>
                </div>
            </div>
        </dialog>
    </div>
@endsection
