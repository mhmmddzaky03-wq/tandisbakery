@extends('layouts.app')
@php use App\Support\FormatHelper; $role='admin'; $active='admin.laba_rugi'; $pageTitle=__('nav.income_statement'); $subtitle=__('nav.financial_reports'); @endphp
@section('content')
<div class="pt-6 bakery-card">
    <div class="bakery-card-header">
        <div class="text-lg font-extrabold">{{ __('nav.income_statement') }}</div>
        <button type="button" class="bakery-btn-ghost" data-print>Cetak</button>
    </div>
    <div class="bakery-card-body grid md:grid-cols-4 gap-4">
        <div class="p-4 bg-slate-50 rounded-2xl"><div class="text-xs text-slate-400">{{ __('page.sales') }}</div><div class="text-xl font-extrabold text-emerald-600">{{ FormatHelper::rupiah($data['sales']) }}</div></div>
        <div class="p-4 bg-slate-50 rounded-2xl"><div class="text-xs text-slate-400">{{ __('page.net_sales') }}</div><div class="text-xl font-extrabold text-emerald-600">{{ FormatHelper::rupiah($data['net_sales']) }}</div></div>
        <div class="p-4 bg-slate-50 rounded-2xl"><div class="text-xs text-slate-400">{{ __('page.gross_profit') }}</div><div class="text-xl font-extrabold text-sky-600">{{ FormatHelper::rupiah($data['gross_profit']) }}</div></div>
        <div class="p-4 bg-slate-50 rounded-2xl"><div class="text-xs text-slate-400">{{ __('page.net_profit') }}</div><div class="text-xl font-extrabold text-amber-600">{{ FormatHelper::rupiah($data['net_profit']) }}</div></div>
    </div>
</div>
@endsection
