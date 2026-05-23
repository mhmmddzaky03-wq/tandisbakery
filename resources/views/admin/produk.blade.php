@extends('layouts.app')

@php
    $title = __('nav.product_data') . ' - Admin';
    $role = 'admin';
    $active = 'admin.produk';
    $pageTitle = __('nav.product_data');
    $subtitle = __('nav.main_menu');
    $storeRoute = 'admin.produk.store';
    $updateRoute = 'admin.produk.update';
    $destroyRoute = 'admin.produk.destroy';
@endphp

@section('content')
    @include('partials.product-page')
@endsection
