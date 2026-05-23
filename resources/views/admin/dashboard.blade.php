@extends('layouts.app')
@php
    use App\Support\FormatHelper;
    $title = 'Dashboard Admin';
    $role = 'admin';
    $active = 'admin.dashboard';
    $pageTitle = __('nav.dashboard');
    $subtitle = __('nav.main_menu');
    $ic = fn ($d) => '<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'" /></svg>';
@endphp
@section('content')
<div class="pt-6">
    <div class="grid gap-4 lg:grid-cols-4">
        <x-kpi-card title="{{ __('page.total_stock_items') }}" :value="(string) $stockCount" :sub="$lowStockCount.' '.__('page.below_min')" tone="rose" :icon="$ic('M7 7h10M7 12h10M7 17h10')" />
        <x-kpi-card title="{{ __('page.production_success_dash') }}" :value="(string) $successProduction" :sub="__('page.from_total').' '.$totalProduction" tone="green" :icon="$ic('M7 12l3 3 7-7')" />
        <x-kpi-card title="{{ __('page.operational_cost_dash') }}" :value="FormatHelper::rupiah($operationalMonth)" :sub="__('page.this_month')" tone="blue" :icon="$ic('M12 3v18')" />
        <x-kpi-card title="{{ __('page.total_sales_dash') }}" :value="FormatHelper::rupiah($salesLast30)" :sub="__('page.last_30_days')" tone="amber" :icon="$ic('M4 7h16')" />
    </div>
    <div class="mt-5 grid gap-5 lg:grid-cols-3">
        <div class="bakery-card lg:col-span-2 p-5">
            <div class="text-sm font-bold mb-3">{{ __('page.sales_trend') }}</div>
            @if ($salesTrend->isNotEmpty())
                <ul class="space-y-2">@foreach($salesTrend as $label => $val)<li class="flex justify-between text-sm"><span>{{ $label }}</span><span class="font-bold text-amber-600">{{ FormatHelper::rupiah($val) }}</span></li>@endforeach</ul>
            @else
                <p class="text-sm text-slate-500">Belum ada data penjualan.</p>
            @endif
        </div>
        <div class="bakery-card p-5">
            <div class="text-sm font-bold mb-3">{{ __('page.cost_composition') }}</div>
            <p class="text-sm">Fixed: <strong>{{ FormatHelper::rupiah($fixedCosts) }}</strong></p>
            <p class="text-sm mt-1">Variable: <strong>{{ FormatHelper::rupiah($variableCosts) }}</strong></p>
        </div>
    </div>
    <div class="mt-5 grid gap-5 lg:grid-cols-3">
        <div class="bakery-card lg:col-span-2 p-5">
            <div class="grid md:grid-cols-3 gap-4">
                <div class="bg-slate-50 p-4 rounded-2xl"><div class="text-xs text-slate-400">{{ __('page.net_profit_dash') }}</div><div class="text-2xl font-extrabold text-emerald-600">{{ FormatHelper::rupiah($netProfit) }}</div></div>
                <div class="bg-slate-50 p-4 rounded-2xl"><div class="text-xs text-slate-400">{{ __('page.cost_label') }}</div><div class="text-2xl font-extrabold text-amber-600">{{ FormatHelper::rupiah($totalCosts) }}</div></div>
                <div class="bg-slate-50 p-4 rounded-2xl"><div class="text-xs text-slate-400">{{ __('page.total_sales_dash') }}</div><div class="text-2xl font-extrabold">{{ FormatHelper::rupiah($salesMonth) }}</div></div>
            </div>
        </div>
        <div class="bakery-card p-5">
            <div class="text-sm font-bold mb-3">{{ __('page.recent_production') }}</div>
            @foreach ($productions as $p)
                <div class="flex justify-between py-2 border-b border-slate-100 text-sm">
                    <span>{{ $p->product_name }}</span>
                    <span class="bakery-badge bg-emerald-50 text-emerald-600">{{ $p->status }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
