@extends('layouts.app')

@php
    $title = $record->product_name.' - Detail Produksi - Karyawan';
    $role = 'karyawan';
    $active = 'karyawan.produksi';
    $pageTitle = $record->product_name;
    $pageSubtitle = $record->id.' · Detail produksi';
    $indexRoute = 'karyawan.produksi';
    $showRoute = 'karyawan.produksi.show';
@endphp

@push('page-actions')
    <x-detail-page-actions :href="route($indexRoute)" label="Data Produksi" />
@endpush

@section('content')
    @include('partials.production-detail-page')
@endsection
