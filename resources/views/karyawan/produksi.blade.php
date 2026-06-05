@extends('layouts.app')

@php
    $title = __('app.pages.production').' - '.__('app.common.employee');
    $role = 'karyawan';
    $active = 'karyawan.produksi';
    $pageTitle = __('app.pages.production');
    $pageSubtitle = __('app.pages.production_subtitle_employee_input');
    $storeRoute = 'karyawan.produksi.store';
    $updateRoute = '';
    $destroyRoute = '';
    $showRoute = 'karyawan.produksi.show';
    $canEdit = false;
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
