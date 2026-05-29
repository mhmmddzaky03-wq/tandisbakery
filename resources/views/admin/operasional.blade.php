@extends('layouts.app')

@php
    $role = 'admin';
    $active = 'admin.operasional';
    $pageTitle = 'Biaya Operasional';
    $pageSubtitle = 'Catat pengeluaran tetap dan variabel per bulan';
    $title = 'Biaya Operasional'.' - Admin';
    $storeRoute = 'admin.operasional.store';
    $updateRoute = 'admin.operasional.update';
    $destroyRoute = 'admin.operasional.destroy';
    $categoryStoreRoute = 'admin.kategori_biaya.store';
    $categoryUpdateRoute = 'admin.kategori_biaya.update';
    $categoryDestroyRoute = 'admin.kategori_biaya.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="cost-baru">
        + Tambah Biaya
    </button>
@endpush

@section('content')
    @include('partials.operational-page')
@endsection
