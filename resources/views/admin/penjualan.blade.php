@extends('layouts.app')

@php
    $role = 'admin';
    $active = 'admin.penjualan';
    $pageTitle = __('app.pages.sales');
    $pageSubtitle = __('app.pages.sales_subtitle');
    $title = __('app.pages.sales').' - '.__('app.common.admin');
    $storeRoute = 'admin.penjualan.store';
    $updateRoute = 'admin.penjualan.update';
    $destroyRoute = 'admin.penjualan.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="sales-baru">
        {{ __('sales.action_add') }}
    </button>
@endpush

@section('content')
    @include('partials.sales-page')
@endsection
