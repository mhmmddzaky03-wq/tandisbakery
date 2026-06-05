@extends('layouts.app')

@php
    $title = __('app.pages.products').' - '.__('app.common.admin');
    $role = 'admin';
    $active = 'admin.produk';
    $pageTitle = __('app.pages.products');
    $pageSubtitle = __('app.pages.products_subtitle');
    $storeRoute = 'admin.produk.store';
    $updateRoute = 'admin.produk.update';
    $destroyRoute = 'admin.produk.destroy';
    $showRoute = 'admin.produk.show';
    $canEdit = true;
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="produk-baru">
        {{ __('product.action_add') }}
    </button>
@endpush

@section('content')
    @include('partials.product-page')
@endsection
