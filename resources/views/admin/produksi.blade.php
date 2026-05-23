@extends('layouts.app')
@php
    $title = __('nav.production_data') . ' - Admin';
    $role = 'admin'; $active = 'admin.produksi'; $pageTitle = __('nav.production_data'); $subtitle = __('nav.main_menu');
    $storeRoute = 'admin.produksi.store'; $updateRoute = 'admin.produksi.update'; $destroyRoute = 'admin.produksi.destroy';
    $canEdit = true;
@endphp
@section('content') @include('partials.production-page') @endsection
