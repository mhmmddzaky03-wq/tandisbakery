@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.laba_rugi';
    $pageTitle = __('nav.income_statement');
    $pageSubtitle = __('page.is_subtitle');
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-ghost whitespace-nowrap" data-print>{{ __('page.print') }}</button>
@endpush

@section('content')
<div class="bakery-card">
    <div class="bakery-card-body grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-slate-50 p-4"><div class="text-xs text-slate-400">{{ __('page.sales') }}</div><div class="mt-1 text-xl font-extrabold text-emerald-600">{{ FormatHelper::rupiah($data['sales']) }}</div></div>
        <div class="rounded-2xl bg-slate-50 p-4"><div class="text-xs text-slate-400">{{ __('page.net_sales') }}</div><div class="mt-1 text-xl font-extrabold text-emerald-600">{{ FormatHelper::rupiah($data['net_sales']) }}</div></div>
        <div class="rounded-2xl bg-slate-50 p-4"><div class="text-xs text-slate-400">{{ __('page.gross_profit') }}</div><div class="mt-1 text-xl font-extrabold text-sky-600">{{ FormatHelper::rupiah($data['gross_profit']) }}</div></div>
        <div class="rounded-2xl bg-slate-50 p-4"><div class="text-xs text-slate-400">{{ __('page.net_profit') }}</div><div class="mt-1 text-xl font-extrabold text-amber-600">{{ FormatHelper::rupiah($data['net_profit']) }}</div></div>
    </div>
</div>
@endsection
