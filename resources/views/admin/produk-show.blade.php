@extends('layouts.app')

@php
    $title = $product->nama.' - Detail Produk - Admin';
    $role = 'admin';
    $active = 'admin.produk';
    $pageTitle = $product->nama;
    $pageSubtitle = $product->id.' · Detail produk';
    $indexRoute = 'admin.produk';
    $showRoute = 'admin.produk.show';
    $updateRoute = 'admin.produk.update';
    $destroyRoute = 'admin.produk.destroy';
    $productionShowRoute = 'admin.produksi.show';
@endphp

@push('page-actions')
    <x-detail-page-actions :href="route($indexRoute)" label="Data Produk">
        <x-slot:toolbar>
            <form id="delete-produk-{{ $product->id }}" method="POST" action="{{ route($destroyRoute, $product->id) }}" class="contents">
                @csrf @method('DELETE')
            </form>
            <button
                type="button"
                class="bakery-toolbar-btn bakery-toolbar-btn-danger"
                data-delete-form="delete-produk-{{ $product->id }}"
                data-confirm-message="Hapus produk ini? Riwayat produksi tidak akan ikut terhapus."
                onclick="handleConfirmDelete(this)"
            >
                <x-icons.trash />
                <span class="hidden sm:inline">Hapus</span>
            </button>
            <button type="button" class="bakery-toolbar-btn bakery-toolbar-btn-primary" data-modal-open="edit-produk-{{ $product->id }}">
                <x-icons.pencil />
                Edit
            </button>
        </x-slot:toolbar>
    </x-detail-page-actions>
@endpush

@section('content')
    @include('partials.product-detail-page')
    @include('partials.product-edit-modal', ['product' => $product])
@endsection
