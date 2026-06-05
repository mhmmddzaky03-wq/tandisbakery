@extends('layouts.app')

@php
    $title = $product->nama.' - '.__('app.pages.products').' - '.__('app.common.admin');
    $role = 'admin';
    $active = 'admin.produk';
    $pageTitle = $product->nama;
    $pageSubtitle = __('app.pages.detail_product', ['id' => $product->id]);
    $indexRoute = 'admin.produk';
    $showRoute = 'admin.produk.show';
    $updateRoute = 'admin.produk.update';
    $destroyRoute = 'admin.produk.destroy';
    $productionShowRoute = 'admin.produksi.show';
@endphp

@push('page-actions')
    <x-detail-page-actions :href="route($indexRoute)" :label="__('product.back')">
        <x-slot:toolbar>
            <form id="delete-produk-{{ $product->id }}" method="POST" action="{{ route($destroyRoute, $product->id) }}" class="contents">
                @csrf @method('DELETE')
            </form>
            <button
                type="button"
                class="bakery-toolbar-btn bakery-toolbar-btn-danger"
                data-delete-form="delete-produk-{{ $product->id }}"
                data-confirm-message="{{ __('product.confirm_delete') }}"
                onclick="handleConfirmDelete(this)"
            >
                <x-icons.trash />
                <span class="hidden sm:inline">{{ __('app.common.delete') }}</span>
            </button>
            <button type="button" class="bakery-toolbar-btn bakery-toolbar-btn-primary" data-modal-open="edit-produk-{{ $product->id }}">
                <x-icons.pencil />
                {{ __('app.common.edit') }}
            </button>
        </x-slot:toolbar>
    </x-detail-page-actions>
@endpush

@section('content')
    @include('partials.product-detail-page')
    @include('partials.product-edit-modal', ['product' => $product])
@endsection
