@extends('layouts.app')

@php
    $title = __('app.pages.products').' - '.__('app.common.employee');
    $role = 'karyawan';
    $active = 'karyawan.produk';
    $pageTitle = __('app.pages.products');
    $pageSubtitle = __('app.pages.products_subtitle');
    $showRoute = 'karyawan.produk.show';
    $canEdit = false;
@endphp

@section('content')
    @include('partials.product-page')
@endsection
