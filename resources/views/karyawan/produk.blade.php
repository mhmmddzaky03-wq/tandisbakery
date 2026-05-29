@extends('layouts.app')

@php
    $title = 'Data Produk'.' - Karyawan';
    $role = 'karyawan';
    $active = 'karyawan.produk';
    $pageTitle = 'Data Produk';
    $pageSubtitle = 'Daftarkan produk berdasarkan riwayat produksi berhasil';
    $canEdit = false;
@endphp

@section('content')
    @include('partials.product-page')
@endsection
