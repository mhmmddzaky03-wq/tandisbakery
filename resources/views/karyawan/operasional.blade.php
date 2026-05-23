@extends('layouts.app')
@php $role='karyawan'; $active='karyawan.operasional'; $pageTitle=__('nav.input_operational'); $subtitle=__('nav.main_menu'); $title=__('nav.input_operational').' - Karyawan';
$storeRoute='karyawan.operasional.store'; $updateRoute='karyawan.operasional.update'; $destroyRoute='karyawan.operasional.destroy'; @endphp
@section('content') @include('partials.operational-page') @endsection
