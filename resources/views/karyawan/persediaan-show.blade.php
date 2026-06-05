@extends('layouts.app')

@php
    $title = $material->nama.' - Detail Persediaan - Karyawan';
    $role = 'karyawan';
    $active = 'karyawan.persediaan';
    $pageTitle = $material->nama;
    $pageSubtitle = __('app.pages.detail_raw_material', ['id' => $material->id]);
    $indexRoute = 'karyawan.persediaan';
    $showRoute = 'karyawan.persediaan.show';
    $updateRoute = 'karyawan.persediaan.update';
    $restockRoute = 'karyawan.persediaan.restock';
    $destroyRoute = 'karyawan.persediaan.destroy';
@endphp

@push('page-actions')
    <x-detail-page-actions :href="route($indexRoute)" :label="__('stock.back_inventory')">
        <x-slot:toolbar>
            <button type="button" class="bakery-toolbar-btn bakery-toolbar-btn-secondary" data-modal-open="restock-stok-{{ $material->id }}">
                <x-icons.restock />
                {{ __('stock.restock') }}
            </button>
            <button type="button" class="bakery-toolbar-btn bakery-toolbar-btn-primary" data-modal-open="edit-stok-{{ $material->id }}">
                <x-icons.pencil />
                {{ __('app.common.edit') }}
            </button>
        </x-slot:toolbar>
    </x-detail-page-actions>
@endpush

@section('content')
    @include('partials.stock-detail-page')
@endsection
