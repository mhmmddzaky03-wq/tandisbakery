@extends('layouts.app')

@php
    $role = 'karyawan';
    $active = 'karyawan.penjualan';
    $pageTitle = __('app.pages.sales');
    $pageSubtitle = __('app.pages.sales_subtitle_employee');
    $title = __('app.pages.sales').' - '.__('app.common.employee');
    $storeRoute = 'karyawan.penjualan.store';
    $updateRoute = '';
    $destroyRoute = '';
    $canEdit = false;
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="sales-baru">
        {{ __('sales.action_add') }}
    </button>
@endpush

@section('content')
    @include('partials.sales-page')
@endsection
