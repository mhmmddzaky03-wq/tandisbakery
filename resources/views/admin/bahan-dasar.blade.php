@extends('layouts.app')

@php
    $title = 'Bahan Dasar - Admin';
    $role = 'admin';
    $active = 'admin.bahan_dasar';
    $pageTitle = __('app.pages.base_materials');
    $pageSubtitle = __('app.pages.base_materials_subtitle');
    $storeRoute = 'admin.bahan_dasar.store';
    $updateRoute = 'admin.bahan_dasar.update';
    $destroyRoute = 'admin.bahan_dasar.destroy';
    $showRoute = 'admin.bahan_dasar.show';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="bahan-dasar-baru">
        {{ __('bahan_dasar.action_add') }}
    </button>
@endpush

@section('content')
    @include('partials.bahan-dasar-page')
@endsection
