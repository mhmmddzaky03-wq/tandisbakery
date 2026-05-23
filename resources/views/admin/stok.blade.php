@extends('layouts.app')
@php
    $title = __('nav.raw_material_stock') . ' - Admin';
    $role = 'admin'; $active = 'admin.stok'; $pageTitle = __('nav.raw_material_stock'); $subtitle = __('nav.main_menu');
    $storeRoute = 'admin.stok.store'; $updateRoute = 'admin.stok.update'; $destroyRoute = 'admin.stok.destroy';
@endphp
@section('content') @include('partials.stock-page') @endsection
