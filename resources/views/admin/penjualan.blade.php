@extends('layouts.app')
@php
    $role='admin'; $active='admin.penjualan'; $pageTitle=__('nav.sales_transactions'); $subtitle=__('nav.main_menu');
    $title=__('nav.sales_transactions').' - Admin';
    $storeRoute='admin.penjualan.store'; $updateRoute='admin.penjualan.update'; $destroyRoute='admin.penjualan.destroy';
@endphp
@section('content') @include('partials.sales-page') @endsection
