@extends('layouts.app')

@php
    $title = $material->nama.' - Detail Stok - Admin';
    $role = 'admin';
    $active = 'admin.stok';
    $pageTitle = $material->nama;
    $pageSubtitle = $material->id.' · Detail bahan baku';
    $indexRoute = 'admin.stok';
    $showRoute = 'admin.stok.show';
    $updateRoute = 'admin.stok.update';
    $restockRoute = 'admin.stok.restock';
    $destroyRoute = 'admin.stok.destroy';
@endphp

@push('page-actions')
    <x-detail-page-actions :href="route($indexRoute)" label="Data Stok">
        <x-slot:toolbar>
            <button type="button" class="bakery-toolbar-btn bakery-toolbar-btn-secondary" data-modal-open="restock-stok-{{ $material->id }}">
                <x-icons.restock />
                Restock
            </button>
            <button type="button" class="bakery-toolbar-btn bakery-toolbar-btn-primary" data-modal-open="edit-stok-{{ $material->id }}">
                <x-icons.pencil />
                Edit
            </button>
        </x-slot:toolbar>
    </x-detail-page-actions>
@endpush

@section('content')
    @include('partials.stock-detail-page')
@endsection
