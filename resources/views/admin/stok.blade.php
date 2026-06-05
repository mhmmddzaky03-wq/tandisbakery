@extends('layouts.app')

@php
    $title = 'Stok Bahan Baku'.' - Admin';
    $role = 'admin';
    $active = 'admin.stok';
    $pageTitle = __('app.pages.raw_materials');
    $pageSubtitle = __('app.pages.raw_materials_subtitle');
    $storeRoute = 'admin.stok.store';
    $indexRoute = 'admin.stok';
    $updateRoute = 'admin.stok.update';
    $restockRoute = 'admin.stok.restock';
    $showRoute = 'admin.stok.show';
    $destroyRoute = 'admin.stok.destroy';
    $unitStoreRoute = 'admin.satuan.store';
    $unitDestroyRoute = 'admin.satuan.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="stok-baru">
        {{ __('stock.action_add') }}
    </button>
@endpush

@section('content')
    @include('partials.stock-page')
@endsection
