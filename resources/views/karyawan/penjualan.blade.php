@extends('layouts.app')
@php
    $role='karyawan'; $active='karyawan.penjualan'; $pageTitle=__('nav.input_sales'); $subtitle=__('nav.main_menu');
    $title=__('nav.input_sales').' - Karyawan';
    $storeRoute='karyawan.penjualan.store'; $updateRoute='karyawan.penjualan.update'; $destroyRoute='karyawan.penjualan.destroy';
@endphp
@section('content') @include('partials.sales-page') @endsection
