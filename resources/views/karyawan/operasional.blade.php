@extends('layouts.app')

@php
    $role = 'karyawan';
    $active = 'karyawan.operasional';
    $pageTitle = __('nav.input_operational');
    $pageSubtitle = __('page.cost_list_subtitle');
    $title = __('nav.input_operational').' - Karyawan';
    $storeRoute = 'karyawan.operasional.store';
    $updateRoute = 'karyawan.operasional.update';
    $destroyRoute = 'karyawan.operasional.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="cost-baru">
        {{ __('page.add_cost') }}
    </button>
@endpush

@section('content')
    @include('partials.operational-page')
@endsection
