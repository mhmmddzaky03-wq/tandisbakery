@extends('layouts.app')

@php
    $role = 'karyawan';
    $active = 'karyawan.operasional';
    $pageTitle = __('app.pages.operational_input');
    $pageSubtitle = __('app.pages.operational_input_subtitle');
    $title = __('app.pages.operational_input').' - '.__('app.common.employee');
    $storeRoute = 'karyawan.operasional.store';
    $updateRoute = 'karyawan.operasional.update';
    $destroyRoute = 'karyawan.operasional.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="cost-baru">
        {{ __('operational.action_add') }}
    </button>
@endpush

@section('content')
    @include('partials.operational-page')
@endsection
