@extends('layouts.app')

@php
    $role = 'admin';
    $active = 'admin.operasional';
    $pageTitle = __('nav.operational_costs');
    $pageSubtitle = __('page.cost_list_subtitle');
    $title = __('nav.operational_costs').' - Admin';
    $storeRoute = 'admin.operasional.store';
    $updateRoute = 'admin.operasional.update';
    $destroyRoute = 'admin.operasional.destroy';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-primary whitespace-nowrap" data-modal-open="cost-baru">
        {{ __('page.add_cost') }}
    </button>
@endpush

@section('content')
    @include('partials.operational-page')
@endsection
