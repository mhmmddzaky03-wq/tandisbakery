@extends('layouts.app')

@php
    $title = $record->product_name.' - '.__('app.pages.production').' - '.__('app.common.admin');
    $role = 'admin';
    $active = 'admin.produksi';
    $pageTitle = $record->product_name;
    $pageSubtitle = __('app.pages.detail_production', ['id' => $record->id]);
    $indexRoute = 'admin.produksi';
    $showRoute = 'admin.produksi.show';
    $updateRoute = 'admin.produksi.update';
    $destroyRoute = 'admin.produksi.destroy';
@endphp

@push('page-actions')
    <x-detail-page-actions :href="route($indexRoute)" :label="__('production.back')">
        <x-slot:toolbar>
            <form id="delete-prod-{{ $record->id }}" method="POST" action="{{ route($destroyRoute, $record->id) }}" class="contents">
                @csrf @method('DELETE')
            </form>
            <button
                type="button"
                class="bakery-toolbar-btn bakery-toolbar-btn-danger"
                data-delete-form="delete-prod-{{ $record->id }}"
                data-confirm-message="{{ __('production.confirm_delete') }}"
                onclick="handleConfirmDelete(this)"
            >
                <x-icons.trash />
                <span class="hidden sm:inline">{{ __('app.common.delete') }}</span>
            </button>
            <button type="button" class="bakery-toolbar-btn bakery-toolbar-btn-primary" data-modal-open="edit-prod-{{ $record->id }}">
                <x-icons.pencil />
                {{ __('app.common.edit') }}
            </button>
        </x-slot:toolbar>
    </x-detail-page-actions>
@endpush

@section('content')
    @include('partials.production-detail-page')
    @include('partials.production-edit-modal', ['r' => $record])
@endsection
