@extends('layouts.app')
@php $role='admin'; $active='admin.operasional'; $pageTitle=__('nav.operational_costs'); $subtitle=__('nav.main_menu'); $title=__('nav.operational_costs').' - Admin';
$storeRoute='admin.operasional.store'; $updateRoute='admin.operasional.update'; $destroyRoute='admin.operasional.destroy'; @endphp
@section('content') @include('partials.operational-page') @endsection
