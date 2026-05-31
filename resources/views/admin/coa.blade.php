@extends('layouts.app')

@php
    use App\Support\FormatHelper;

    $role = 'admin';
    $active = 'admin.coa';
    $pageTitle = 'Chart of Accounts';
    $pageSubtitle = 'Chart of Accounts resmi perusahaan (48 akun)';
    $title = 'Chart of Accounts'.' - Admin';

    $groupTone = static fn (string $grup): string => match ($grup) {
        'Asset' => 'bg-sky-50 text-sky-700',
        'Liability' => 'bg-rose-50 text-rose-700',
        'Equity' => 'bg-violet-50 text-violet-700',
        'Revenues' => 'bg-emerald-50 text-emerald-700',
        'Expenses' => 'bg-amber-50 text-amber-700',
        default => 'bg-slate-100 text-slate-600',
    };

    $filterUrl = static function (?string $grup): string {
        $query = array_filter(['grup' => $grup]);

        return route('admin.coa').($query ? '?'.http_build_query($query) : '');
    };
@endphp

@push('page-actions')
    <x-pdf-print-button :route="route('admin.pdf.coa')" :query="array_filter(['grup' => $groupFilter ?? null])" />
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="coa-baru">
        + Tambah Akun
    </button>
@endpush

@section('content')
<div>
    <p class="text-xs font-semibold text-slate-400">COA adalah master daftar akun. Saldo transaksi lihat di General Ledger atau Neraca.</p>

    <div class="mt-4 flex flex-wrap gap-2">
        <a
            href="{{ $filterUrl(null) }}"
            class="rounded-lg px-3 py-1.5 text-xs font-bold transition {{ empty($groupFilter) ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
        >
            Semua ({{ $totalAccountCount }})
        </a>
        @foreach ($coaGroupMap as $grup => $subs)
            <a
                href="{{ $filterUrl($grup) }}"
                class="rounded-lg px-3 py-1.5 text-xs font-bold transition {{ ($groupFilter ?? '') === $grup ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                {{ $grup }}
            </a>
        @endforeach
    </div>

    <div class="bakery-card mt-4" data-table-search>
        <div class="bakery-card-header flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div class="text-lg font-extrabold text-slate-900">Daftar Akun COA</div>
            <x-table-search placeholder="Cari akun..." :value="''" />
        </div>

        <div class="bakery-card-body bakery-table-wrap pt-2">
            <table class="bakery-table">
                <thead>
                    <tr>
                        <th class="w-[90px]">Kode</th>
                        <th>Nama Akun</th>
                        <th class="w-[80px]">Posisi</th>
                        <th class="w-[110px]">Grup</th>
                        <th class="w-[160px]">Sub-Grup</th>
                        <th class="w-[90px] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody data-table-search-body>
                    @forelse ($accounts as $row)
                        @php $acc = $row['account']; @endphp
                        <tr
                            data-searchable-row
                            data-search="{{ strtolower($acc->kode.' '.$acc->nama.' '.$acc->grup.' '.($acc->sub_grup ?? '').' '.$acc->posisi) }}"
                        >
                            <td class="font-bold text-slate-800">{{ $acc->kode }}</td>
                            <td class="font-semibold text-slate-700">{{ $acc->nama }}</td>
                            <td>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $acc->posisi === 'Debit' ? 'bg-sky-50 text-sky-600' : 'bg-violet-50 text-violet-700' }}">
                                    {{ $acc->posisi === 'Debit' ? 'Dr' : 'Cr' }}
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $groupTone($acc->grup) }}">
                                    {{ $acc->grup }}
                                </span>
                            </td>
                            <td class="text-sm text-slate-600">{{ $acc->sub_grup ?? '—' }}</td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <button
                                        type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                                        data-modal-open="detail-coa-{{ $acc->kode }}"
                                        title="Lihat detail"
                                        aria-label="Lihat detail"
                                    >
                                        <x-icons.info-circle class="h-4 w-4" />
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-sky-600"
                                        data-modal-open="edit-coa-{{ $acc->kode }}"
                                        title="Edit"
                                        aria-label="Edit"
                                    >
                                        <x-icons.pencil />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr data-table-empty>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">
                                @if (! empty($groupFilter))
                                    Data tidak ditemukan
                                @else
                                    Belum ada akun.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                    <tr data-table-no-results class="hidden">
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">
                            Data tidak ditemukan
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach ($accounts as $row)
    @php $acc = $row['account']; @endphp
    <x-modal id="detail-coa-{{ $acc->kode }}" size="sm" title="Detail Akun" :subtitle="$acc->kode">
        <dl class="text-sm">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                <dt class="text-slate-400">Nama Akun</dt>
                <dd class="max-w-[60%] text-right font-semibold text-slate-800">{{ $acc->nama }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                <dt class="text-slate-400">Posisi</dt>
                <dd class="font-semibold text-slate-800">{{ $acc->posisi === 'Debit' ? 'Dr' : 'Cr' }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                <dt class="text-slate-400">Grup</dt>
                <dd class="font-semibold text-slate-800">{{ $acc->grup }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-2.5">
                <dt class="text-slate-400">Sub-Grup</dt>
                <dd class="font-semibold text-slate-800">{{ $acc->sub_grup ?? '—' }}</dd>
            </div>
            <div class="flex items-center justify-between gap-4 py-2.5">
                <dt class="text-slate-400">Saldo Saat Ini</dt>
                <dd class="font-semibold text-slate-800">{{ FormatHelper::rupiah($row['saldo']) }}</dd>
            </div>
        </dl>
        <p class="mt-2 text-xs font-semibold text-slate-400">Saldo dihitung otomatis dari jurnal. Bukan saldo awal (Beg. Bal) manual.</p>
        <div class="mt-4 flex justify-end border-t border-slate-100 pt-3">
            <button type="button" class="bakery-btn-ghost text-sm" data-modal-close>Tutup</button>
        </div>
    </x-modal>

    <x-modal id="edit-coa-{{ $acc->kode }}" title="Edit Akun COA" :subtitle="$acc->kode">
        <form
            method="POST"
            action="{{ route('admin.coa.update', $acc->kode) }}"
            class="space-y-4"
            data-modal-form
            data-coa-form
            data-coa-placeholder-sub="Pilih sub-grup"
        >
            @csrf @method('PUT')
            @include('partials.coa-form-fields', [
                'coaGroupMap' => $coaGroupMap,
                'selectedGrup' => $acc->grup,
                'selectedSubGrup' => $acc->sub_grup,
                'nama' => $acc->nama,
                'posisi' => $acc->posisi,
            ])
            <x-form-actions />
        </form>
    </x-modal>
@endforeach

<x-modal id="coa-baru" title="+ Tambah Akun" subtitle="Kode harus unik sesuai COA perusahaan" :auto-open="$errors->has('kode') || $errors->has('grup')">
    <form method="POST" action="{{ route('admin.coa.store') }}" class="space-y-4" data-modal-form data-coa-form data-coa-placeholder-sub="Pilih sub-grup">
        @csrf
        @include('partials.coa-form-fields', [
            'coaGroupMap' => $coaGroupMap,
            'selectedGrup' => old('grup'),
            'selectedSubGrup' => old('sub_grup'),
            'showCode' => true,
        ])
        <x-form-actions />
    </form>
</x-modal>

<script type="application/json" id="coa-group-map-data">@json($coaGroupMap)</script>
@endsection
