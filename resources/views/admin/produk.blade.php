@extends('layouts.app')

@php
    $title = __('nav.product_data').' - Admin';
    $role = 'admin';
    $active = 'admin.produk';
    $pageTitle = __('nav.product_data');
    $pageSubtitle = __('page.product_list_subtitle');
    $storeRoute = 'admin.produk.store';
    $updateRoute = 'admin.produk.update';
    $destroyRoute = 'admin.produk.destroy';
    $canEdit = true;
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="produk-baru">
        {{ __('page.add_data') }}
    </button>
@endpush

@section('content')
    @include('partials.product-page')
@endsection
