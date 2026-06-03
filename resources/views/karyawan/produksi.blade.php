@extends('layouts.app')

@php
    $title = 'Data Produksi'.' - Karyawan';
    $role = 'karyawan';
    $active = 'karyawan.produksi';
    $pageTitle = 'Data Produksi';
    $pageSubtitle = 'Input dan lihat data produksi';
    $storeRoute = 'karyawan.produksi.store';
    $updateRoute = '';
    $destroyRoute = '';
    $showRoute = 'karyawan.produksi.show';
    $canEdit = false;
    $canAdd = true;
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="prod-baru">
        + Tambah Produksi
    </button>
@endpush

@section('content')
    @include('partials.production-page')
@endsection
