@extends('layouts.app')

@php
    $title = $record->product_name.' - '.__('app.pages.production').' - '.__('app.common.employee');
    $role = 'karyawan';
    $active = 'karyawan.produksi';
    $pageTitle = $record->product_name;
    $pageSubtitle = __('app.pages.detail_production', ['id' => $record->id]);
    $indexRoute = 'karyawan.produksi';
    $showRoute = 'karyawan.produksi.show';
@endphp

@push('page-actions')
    <x-detail-page-actions :href="route($indexRoute)" :label="__('production.back')" />
@endpush

@section('content')
    @include('partials.production-detail-page')
@endsection
