@extends('layouts.app')

@php
    $role = 'admin';
    $active = 'admin.penjualan';
    $pageTitle = __('nav.sales_transactions');
    $pageSubtitle = __('page.sales_list_subtitle');
    $title = __('nav.sales_transactions').' - Admin';
    $storeRoute = 'admin.penjualan.store';
    $updateRoute = 'admin.penjualan.update';
    $destroyRoute = 'admin.penjualan.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="sales-baru">
        {{ __('page.add_transaction') }}
    </button>
@endpush

@section('content')
    @include('partials.sales-page')
@endsection
