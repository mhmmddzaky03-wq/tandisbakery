@extends('layouts.app')

@php
    $role = 'karyawan';
    $active = 'karyawan.penjualan';
    $pageTitle = 'Transaksi Penjualan';
    $pageSubtitle = 'Input dan lihat transaksi penjualan';
    $title = 'Transaksi Penjualan'.' - Karyawan';
    $storeRoute = 'karyawan.penjualan.store';
    $updateRoute = '';
    $destroyRoute = '';
    $canEdit = false;
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="sales-baru">
        + Tambah Transaksi
    </button>
@endpush

@section('content')
    @include('partials.sales-page')
@endsection
