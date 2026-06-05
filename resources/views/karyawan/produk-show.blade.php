@extends('layouts.app')

@php
    $title = $product->nama.' - '.__('app.pages.products').' - '.__('app.common.employee');
    $role = 'karyawan';
    $active = 'karyawan.produk';
    $pageTitle = $product->nama;
    $pageSubtitle = __('app.pages.detail_product', ['id' => $product->id]);
    $indexRoute = 'karyawan.produk';
    $showRoute = 'karyawan.produk.show';
    $productionShowRoute = 'karyawan.produksi.show';
@endphp

@push('page-actions')
    <x-detail-page-actions :href="route($indexRoute)" :label="__('product.back')" />
@endpush

@section('content')
    @include('partials.product-detail-page')
@endsection
