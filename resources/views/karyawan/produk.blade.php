@extends('layouts.app')

@php
    $title = __('nav.product_data') . ' - Karyawan';
    $role = 'karyawan';
    $active = 'karyawan.produk';
    $pageTitle = __('nav.product_data');
    $subtitle = __('nav.main_menu');
    $storeRoute = 'karyawan.produk.store';
    $updateRoute = 'karyawan.produk.update';
    $destroyRoute = 'karyawan.produk.destroy';
@endphp

@section('content')
    @include('partials.product-page')
@endsection
