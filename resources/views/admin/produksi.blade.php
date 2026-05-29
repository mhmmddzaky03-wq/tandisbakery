@extends('layouts.app')

@php
    $title = __('nav.production_data').' - Admin';
    $role = 'admin';
    $active = 'admin.produksi';
    $pageTitle = __('nav.production_data');
    $pageSubtitle = __('page.production_list_subtitle');
    $storeRoute = 'admin.produksi.store';
    $updateRoute = 'admin.produksi.update';
    $destroyRoute = 'admin.produksi.destroy';
    $canEdit = true;
    $canAdd = true;
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="prod-baru">
        + {{ __('page.add_production') }}
    </button>
@endpush

@section('content')
    @include('partials.production-page')
@endsection
