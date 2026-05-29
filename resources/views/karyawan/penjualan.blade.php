@extends('layouts.app')

@php
    $role = 'karyawan';
    $active = 'karyawan.penjualan';
    $pageTitle = __('nav.input_sales');
    $pageSubtitle = __('page.sales_list_subtitle');
    $title = __('nav.input_sales').' - Karyawan';
    $storeRoute = 'karyawan.penjualan.store';
    $updateRoute = 'karyawan.penjualan.update';
    $destroyRoute = 'karyawan.penjualan.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="sales-baru">
        {{ __('page.add_transaction') }}
    </button>
@endpush

@section('content')
    @include('partials.sales-page')
@endsection
