@extends('layouts.app')

@php
    $role = 'admin';
    $active = 'admin.penjualan';
    $pageTitle = 'Transaksi Penjualan';
    $pageSubtitle = 'Input dan kelola rekap transaksi penjualan harian';
    $title = 'Transaksi Penjualan'.' - Admin';
    $storeRoute = 'admin.penjualan.store';
    $updateRoute = 'admin.penjualan.update';
    $destroyRoute = 'admin.penjualan.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="sales-baru">
        + Tambah Transaksi
    </button>
@endpush

@section('content')
    @include('partials.sales-page')
@endsection
