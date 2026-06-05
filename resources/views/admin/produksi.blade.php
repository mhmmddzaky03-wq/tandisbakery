@extends('layouts.app')

@php
    $title = __('app.pages.production').' - '.__('app.common.admin');
    $role = 'admin';
    $active = 'admin.produksi';
    $pageTitle = __('app.pages.production');
    $pageSubtitle = __('app.pages.production_subtitle');
    $storeRoute = 'admin.produksi.store';
    $updateRoute = 'admin.produksi.update';
    $destroyRoute = 'admin.produksi.destroy';
    $showRoute = 'admin.produksi.show';
    $canEdit = true;
    $canAdd = true;
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="prod-baru">
        {{ __('production.action_add') }}
    </button>
@endpush

@section('content')
    @include('partials.production-page')
@endsection
