@extends('layouts.app')

@php
    $title = 'Bahan Dasar - Admin';
    $role = 'admin';
    $active = 'admin.bahan_dasar';
    $pageTitle = 'Bahan Dasar';
    $pageSubtitle = 'Kelola adonan setengah jadi dari bahan baku';
    $storeRoute = 'admin.bahan_dasar.store';
    $updateRoute = 'admin.bahan_dasar.update';
    $destroyRoute = 'admin.bahan_dasar.destroy';
    $showRoute = 'admin.bahan_dasar.show';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="bahan-dasar-baru">
        + Tambah Bahan Dasar
    </button>
@endpush

@section('content')
    @include('partials.bahan-dasar-page')
@endsection
