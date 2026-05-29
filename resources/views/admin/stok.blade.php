@extends('layouts.app')

@php
    $title = __('nav.raw_material_stock').' - Admin';
    $role = 'admin';
    $active = 'admin.stok';
    $pageTitle = __('nav.raw_material_stock');
    $pageSubtitle = __('page.stock_list_subtitle');
    $storeRoute = 'admin.stok.store';
    $updateRoute = 'admin.stok.update';
    $restockRoute = 'admin.stok.restock';
    $destroyRoute = 'admin.stok.destroy';
    $unitStoreRoute = 'admin.satuan.store';
    $unitDestroyRoute = 'admin.satuan.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="stok-baru">
        {{ __('page.add_stock') }}
    </button>
@endpush

@section('content')
    @include('partials.stock-page')
@endsection
