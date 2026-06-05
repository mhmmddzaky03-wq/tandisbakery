@extends('layouts.app')

@php
    $title = 'Input Data Persediaan'.' - Karyawan';
    $role = 'karyawan';
    $active = 'karyawan.persediaan';
    $pageTitle = __('app.pages.inventory_input');
    $pageSubtitle = __('app.pages.inventory_input_subtitle');
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
        {{ __('stock.action_add') }}
    </button>
@endpush

@section('content')
    @include('partials.stock-page')
@endsection
