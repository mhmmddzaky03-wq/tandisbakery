@extends('layouts.app')
@php
    $title = __('nav.input_production') . ' - Karyawan';
    $role = 'karyawan'; $active = 'karyawan.produksi'; $pageTitle = __('nav.input_production'); $subtitle = __('nav.main_menu');
    $storeRoute = 'karyawan.produksi.store'; $updateRoute = ''; $destroyRoute = '';
    $canEdit = false;
@endphp
@section('content') @include('partials.production-page') @endsection
