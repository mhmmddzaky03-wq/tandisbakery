@extends('layouts.app')

@php
    $title = $item->nama.' - Bahan Dasar - Admin';
    $role = 'admin';
    $active = 'admin.bahan_dasar';
    $pageTitle = $item->nama;
    $pageSubtitle = $item->id.' · Detail bahan dasar';
    $indexRoute = 'admin.bahan_dasar';
    $showRoute = 'admin.bahan_dasar.show';
    $updateRoute = 'admin.bahan_dasar.update';
    $destroyRoute = 'admin.bahan_dasar.destroy';
    $buatAdonanRoute = 'admin.bahan_dasar.buat_adonan';
    $destroyBatchRoute = 'admin.bahan_dasar.batch.destroy';
@endphp

@push('page-actions')
    <x-detail-page-actions :href="route($indexRoute)" label="Bahan Dasar">
        <x-slot:toolbar>
            <form id="delete-bahan-dasar-{{ $item->id }}" method="POST" action="{{ route($destroyRoute, $item->id) }}" class="contents">
                @csrf @method('DELETE')
            </form>
            @if ($item->canBeDeleted())
                <button
                    type="button"
                    class="bakery-toolbar-btn bakery-toolbar-btn-danger"
                    data-delete-form="delete-bahan-dasar-{{ $item->id }}"
                    data-confirm-message="Hapus bahan dasar ini?"
                    onclick="handleConfirmDelete(this)"
                >
                    <x-icons.trash />
                    <span class="hidden sm:inline">Hapus</span>
                </button>
            @endif
            <button type="button" class="bakery-toolbar-btn bakery-toolbar-btn-secondary" data-modal-open="buat-adonan-{{ $item->id }}">
                <x-icons.plus class="h-4 w-4" />
                Buat Adonan
            </button>
            <button type="button" class="bakery-toolbar-btn bakery-toolbar-btn-primary" data-modal-open="edit-bahan-dasar-{{ $item->id }}">
                <x-icons.pencil />
                Edit
            </button>
        </x-slot:toolbar>
    </x-detail-page-actions>
@endpush

@section('content')
    @include('partials.bahan-dasar-detail-page')
@endsection
