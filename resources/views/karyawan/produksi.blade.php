@extends('layouts.app')

@php
    $title = __('nav.input_production').' - Karyawan';
    $role = 'karyawan';
    $active = 'karyawan.produksi';
    $pageTitle = __('nav.input_production');
    $pageSubtitle = __('page.production_list_subtitle');
    $storeRoute = 'karyawan.produksi.store';
    $updateRoute = '';
    $destroyRoute = '';
    $canEdit = false;
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
