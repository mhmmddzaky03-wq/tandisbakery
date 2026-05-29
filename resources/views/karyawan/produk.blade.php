@extends('layouts.app')

@php
    $title = __('nav.product_data').' - Karyawan';
    $role = 'karyawan';
    $active = 'karyawan.produk';
    $pageTitle = __('nav.product_data');
    $pageSubtitle = __('page.product_list_subtitle');
    $canEdit = false;
@endphp

@section('content')
    @include('partials.product-page')
@endsection
