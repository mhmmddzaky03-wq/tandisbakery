@extends('layouts.app')

@php
    $title = $record->product_name.' - Detail Produksi - Admin';
    $role = 'admin';
    $active = 'admin.produksi';
    $pageTitle = $record->product_name;
    $pageSubtitle = $record->id.' · Detail produksi';
    $indexRoute = 'admin.produksi';
    $showRoute = 'admin.produksi.show';
    $updateRoute = 'admin.produksi.update';
    $destroyRoute = 'admin.produksi.destroy';
@endphp

@push('page-actions')
    <x-detail-page-actions :href="route($indexRoute)" label="Data Produksi">
        <x-slot:toolbar>
            <form id="delete-prod-{{ $record->id }}" method="POST" action="{{ route($destroyRoute, $record->id) }}" class="contents">
                @csrf @method('DELETE')
            </form>
            <button
                type="button"
                class="bakery-toolbar-btn bakery-toolbar-btn-danger"
                data-delete-form="delete-prod-{{ $record->id }}"
                data-confirm-message="Hapus data produksi ini?"
                onclick="handleConfirmDelete(this)"
            >
                <x-icons.trash />
                <span class="hidden sm:inline">Hapus</span>
            </button>
            <button type="button" class="bakery-toolbar-btn bakery-toolbar-btn-primary" data-modal-open="edit-prod-{{ $record->id }}">
                <x-icons.pencil />
                Edit
            </button>
        </x-slot:toolbar>
    </x-detail-page-actions>
@endpush

@section('content')
    @include('partials.production-detail-page')
    @include('partials.production-edit-modal', ['r' => $record])
@endsection
