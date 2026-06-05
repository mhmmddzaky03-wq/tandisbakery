@extends('layouts.app')

@php
    $role = 'admin';
    $active = 'admin.operasional';
    $pageTitle = __('app.pages.operational_costs');
    $pageSubtitle = __('app.pages.operational_costs_subtitle');
    $title = __('app.pages.operational_costs').' - '.__('app.common.admin');
    $storeRoute = 'admin.operasional.store';
    $updateRoute = 'admin.operasional.update';
    $destroyRoute = 'admin.operasional.destroy';
    $categoryStoreRoute = 'admin.kategori_biaya.store';
    $categoryUpdateRoute = 'admin.kategori_biaya.update';
    $categoryDestroyRoute = 'admin.kategori_biaya.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="cost-baru">
        {{ __('operational.action_add') }}
    </button>
@endpush

@section('content')
    @include('partials.operational-page')
@endsection
