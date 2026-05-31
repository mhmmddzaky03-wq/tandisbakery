@extends('layouts.app')

@php
    $title = $product->nama.' - Detail Produk - Karyawan';
    $role = 'karyawan';
    $active = 'karyawan.produk';
    $pageTitle = $product->nama;
    $pageSubtitle = $product->id.' · Detail produk';
    $indexRoute = 'karyawan.produk';
    $showRoute = 'karyawan.produk.show';
    $productionShowRoute = 'karyawan.produksi.show';
@endphp

@push('page-actions')
    <x-detail-page-actions :href="route($indexRoute)" label="Data Produk" />
@endpush

@section('content')
    @include('partials.product-detail-page')
@endsection
