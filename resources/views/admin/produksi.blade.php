@extends('layouts.app')

@php
    $title = 'Data Produksi'.' - Admin';
    $role = 'admin';
    $active = 'admin.produksi';
    $pageTitle = 'Data Produksi';
    $pageSubtitle = 'Kelola data produksi dan penggunaan bahan baku';
    $storeRoute = 'admin.produksi.store';
    $updateRoute = 'admin.produksi.update';
    $destroyRoute = 'admin.produksi.destroy';
    $canEdit = true;
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
