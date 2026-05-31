@extends('layouts.app')

@php
    $title = 'Input Data Persediaan'.' - Karyawan';
    $role = 'karyawan';
    $active = 'karyawan.persediaan';
    $pageTitle = 'Input Data Persediaan';
    $pageSubtitle = 'Kelola stok bahan baku untuk produksi';
    $storeRoute = 'karyawan.persediaan.store';
    $indexRoute = 'karyawan.persediaan';
    $updateRoute = 'karyawan.persediaan.update';
    $restockRoute = 'karyawan.persediaan.restock';
    $showRoute = 'karyawan.persediaan.show';
    $destroyRoute = 'karyawan.persediaan.destroy';
    $unitStoreRoute = 'karyawan.satuan.store';
    $unitDestroyRoute = 'karyawan.satuan.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="stok-baru">
        + Tambah Stok Baru
    </button>
@endpush

@section('content')
    @include('partials.stock-page')
@endsection
