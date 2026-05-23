@extends('layouts.app')
@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.coa';
    $pageTitle = __('nav.coa');
    $subtitle = __('nav.accounting');
    $title = 'COA - Admin';
@endphp
@section('content')
<div class="pt-6 bakery-card">
    <div class="bakery-card-header">
        <div class="text-lg font-extrabold">{{ __('nav.coa') }}</div>
        <div class="flex gap-2">
            <button type="button" class="bakery-btn-ghost" data-print>Cetak</button>
            <button type="button" class="bakery-btn-primary" data-modal-open="coa-baru">+ Akun</button>
        </div>
    </div>
    <div class="bakery-card-body">
        <form method="GET" class="mb-4 flex gap-2"><input class="bakery-input flex-1" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('page.search_account') }}" /><button class="bakery-btn-ghost">Cari</button></form>
        <table class="bakery-table">
            <thead><tr><th>Kode</th><th>Nama</th><th>Posisi</th><th>Grup</th><th>Saldo</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach ($accounts as $row)
                    <tr>
                        <td class="font-bold">{{ $row['account']->kode }}</td>
                        <td>{{ $row['account']->nama }}</td>
                        <td>{{ $row['account']->posisi }}</td>
                        <td>{{ $row['account']->grup }}</td>
                        <td class="font-extrabold">{{ FormatHelper::rupiah($row['saldo']) }}</td>
                        <td><button type="button" class="bakery-btn-ghost text-xs" data-modal-open="edit-coa-{{ $row['account']->kode }}">Edit</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@foreach ($accounts as $row)
    <x-modal id="edit-coa-{{ $row['account']->kode }}" title="Ubah Akun" :subtitle="$row['account']->kode">
        <form method="POST" action="{{ route('admin.coa.update', $row['account']->kode) }}" data-modal-form>
            @csrf @method('PUT')
            <x-form-field label="Nama akun" name="nama" :value="$row['account']->nama" required autofocus />
            <x-form-field label="Posisi normal" name="posisi" type="select" required helper="Saldo normal akun ini bertambah di sisi mana">
                <option value="Debit" @selected($row['account']->posisi === 'Debit')>Debit (Dr)</option>
                <option value="Credit" @selected($row['account']->posisi === 'Credit')>Kredit (Cr)</option>
            </x-form-field>
            <x-form-field label="Grup laporan" name="grup" :value="$row['account']->grup" required placeholder="Current Asset" />
            <x-form-actions />
        </form>
    </x-modal>
@endforeach

<x-modal id="coa-baru" title="Tambah Akun (COA)" subtitle="Chart of Accounts — kode harus unik" :auto-open="$errors->has('kode')">
    <form method="POST" action="{{ route('admin.coa.store') }}" data-modal-form>
        @csrf
        <x-form-field label="Kode akun" name="kode" :value="old('kode')" required autofocus placeholder="1-999" helper="Format: 1-110, 4-110, 5-150, dll." />
        <x-form-field label="Nama akun" name="nama" :value="old('nama')" required />
        <x-form-field label="Posisi normal" name="posisi" type="select" required>
            <option value="Debit" @selected(old('posisi') === 'Debit')>Debit (Dr)</option>
            <option value="Credit" @selected(old('posisi', 'Credit') === 'Credit')>Kredit (Cr)</option>
        </x-form-field>
        <x-form-field label="Grup laporan" name="grup" :value="old('grup')" required helper="Contoh: Current Asset, Revenue, Expenses" />
        <x-form-actions />
    </form>
</x-modal>
@endsection
