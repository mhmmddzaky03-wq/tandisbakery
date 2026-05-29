@extends('layouts.app')

@php
    $title = 'Data Produk'.' - Admin';
    $role = 'admin';
    $active = 'admin.produk';
    $pageTitle = 'Data Produk';
    $pageSubtitle = 'Daftarkan produk berdasarkan riwayat produksi berhasil';
    $storeRoute = 'admin.produk.store';
    $updateRoute = 'admin.produk.update';
    $destroyRoute = 'admin.produk.destroy';
    $canEdit = true;
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="produk-baru">
        + Tambah Produk
    </button>
@endpush

@section('content')
    @include('partials.product-page')
@endsection
