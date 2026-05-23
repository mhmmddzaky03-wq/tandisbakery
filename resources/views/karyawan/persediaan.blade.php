@extends('layouts.app')
@php
    $title = __('nav.input_inventory') . ' - Karyawan';
    $role = 'karyawan'; $active = 'karyawan.persediaan'; $pageTitle = __('nav.input_inventory'); $subtitle = __('nav.main_menu');
    $storeRoute = 'karyawan.persediaan.store'; $updateRoute = 'karyawan.persediaan.update'; $destroyRoute = 'karyawan.persediaan.destroy';
@endphp
@section('content') @include('partials.stock-page') @endsection
