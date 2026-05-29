@extends('layouts.app')

@php
    $title = __('nav.input_inventory').' - Karyawan';
    $role = 'karyawan';
    $active = 'karyawan.persediaan';
    $pageTitle = __('nav.input_inventory');
    $pageSubtitle = __('page.stock_list_subtitle');
    $storeRoute = 'karyawan.persediaan.store';
    $updateRoute = 'karyawan.persediaan.update';
    $restockRoute = 'karyawan.persediaan.restock';
    $destroyRoute = 'karyawan.persediaan.destroy';
    $unitStoreRoute = 'karyawan.satuan.store';
    $unitDestroyRoute = 'karyawan.satuan.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="stok-baru">
        {{ __('page.add_stock') }}
    </button>
@endpush

@section('content')
    @include('partials.stock-page')
@endsection
