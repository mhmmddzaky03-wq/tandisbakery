@extends('layouts.app')

@php
    $role = 'karyawan';
    $active = 'karyawan.penjualan';
    $pageTitle = 'Input Data Penjualan';
    $pageSubtitle = 'Input dan kelola rekap transaksi penjualan harian';
    $title = 'Input Data Penjualan'.' - Karyawan';
    $storeRoute = 'karyawan.penjualan.store';
    $updateRoute = 'karyawan.penjualan.update';
    $destroyRoute = 'karyawan.penjualan.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="sales-baru">
        + Tambah Transaksi
    </button>
@endpush

@section('content')
    @include('partials.sales-page')
@endsection
