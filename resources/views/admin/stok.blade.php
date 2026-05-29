@extends('layouts.app')

@php
    $title = 'Stok Bahan Baku'.' - Admin';
    $role = 'admin';
    $active = 'admin.stok';
    $pageTitle = 'Stok Bahan Baku';
    $pageSubtitle = 'Kelola stok bahan baku untuk produksi';
    $storeRoute = 'admin.stok.store';
    $updateRoute = 'admin.stok.update';
    $restockRoute = 'admin.stok.restock';
    $destroyRoute = 'admin.stok.destroy';
    $unitStoreRoute = 'admin.satuan.store';
    $unitDestroyRoute = 'admin.satuan.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="stok-baru">
        + Tambah Stok Baru
    </button>
@endpush

@section('content')
    @include('partials.stock-page')
@endsection
