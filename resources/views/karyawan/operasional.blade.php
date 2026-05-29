@extends('layouts.app')

@php
    $role = 'karyawan';
    $active = 'karyawan.operasional';
    $pageTitle = 'Input Data Operasional';
    $pageSubtitle = 'Catat pengeluaran tetap dan variabel per bulan';
    $title = 'Input Data Operasional'.' - Karyawan';
    $storeRoute = 'karyawan.operasional.store';
    $updateRoute = 'karyawan.operasional.update';
    $destroyRoute = 'karyawan.operasional.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="cost-baru">
        + Tambah Biaya
    </button>
@endpush

@section('content')
    @include('partials.operational-page')
@endsection
