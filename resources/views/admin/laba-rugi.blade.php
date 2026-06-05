@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.laba_rugi';
    $pageTitle = __('app.pages.income_statement');
    $pageSubtitle = $filterLabel ?? __('app.pages.income_statement_subtitle');
    $isProfit = $data['net_profit'] >= 0;
@endphp

@push('page-actions')
    <x-pdf-print-button
        :route="route('admin.pdf.laba_rugi')"
        :query="['from' => $from, 'to' => $to]"
    />
@endpush

@section('content')
<div class="bakery-page">
    <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
        <div class="min-w-[140px] flex-1 sm:flex-none">
            <label for="from" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('app.common.from') }}</label>
            <input type="date" id="from" name="from" value="{{ $from }}" class="bakery-input" onchange="this.form.submit()" />
        </div>
        <div class="min-w-[140px] flex-1 sm:flex-none">
            <label for="to" class="mb-1.5 block text-xs font-bold text-slate-600">{{ __('app.common.to') }}</label>
            <input type="date" id="to" name="to" value="{{ $to }}" class="bakery-input" onchange="this.form.submit()" />
        </div>
    </form>

    <div class="bakery-grid-kpi">
        <div class="bakery-card p-4 sm:p-5">
            <p class="text-xs font-semibold text-slate-500">{{ __('reports.income_statement.total_revenue') }}</p>
            <p class="mt-1 text-lg font-extrabold tabular-nums text-emerald-700 sm:text-xl">{{ FormatHelper::rupiah($data['sales']) }}</p>
        </div>
        <div class="bakery-card p-4 sm:p-5">
            <p class="text-xs font-semibold text-slate-500">{{ __('reports.income_statement.gross_profit') }}</p>
            <p class="mt-1 text-lg font-extrabold tabular-nums sm:text-xl {{ $data['gross_profit'] >= 0 ? 'text-sky-700' : 'text-rose-600' }}">
                {{ FormatHelper::rupiah($data['gross_profit']) }}
            </p>
        </div>
        <div class="bakery-card p-4 sm:p-5">
            <p class="text-xs font-semibold text-slate-500">{{ __('reports.income_statement.total_operating') }}</p>
            <p class="mt-1 text-lg font-extrabold tabular-nums text-slate-800 sm:text-xl">{{ FormatHelper::rupiah($data['total_operating_expenses']) }}</p>
        </div>
        <div class="bakery-card p-4 sm:p-5 {{ $isProfit ? 'bg-emerald-50/80 ring-emerald-200/60' : 'bg-rose-50/80 ring-rose-200/60' }}">
            <p class="text-xs font-semibold {{ $isProfit ? 'text-emerald-800/80' : 'text-rose-800/80' }}">{{ __('reports.income_statement.net_profit') }}</p>
            <p class="mt-1 text-lg font-extrabold tabular-nums sm:text-xl {{ $isProfit ? 'text-emerald-900' : 'text-rose-900' }}">
                {{ FormatHelper::rupiah($data['net_profit']) }}
            </p>
        </div>
    </div>

    <div class="bakery-card overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50 px-4 py-3 sm:px-6">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ __('reports.income_statement.title') }}</p>
            <p class="mt-0.5 text-sm text-slate-600">
                {{ __('reports.income_statement.subtitle', ['from' => FormatHelper::dateId($from), 'to' => FormatHelper::dateId($to)]) }}
            </p>
        </div>

        <div class="bakery-card-body">
            <div class="bakery-table-wrap">
                <table class="bakery-table text-sm">
                    <thead>
                        <tr>
                            <th class="min-w-[280px]">{{ __('reports.pos') }}</th>
                            <th class="w-[140px] text-right">{{ __('reports.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-slate-50/90">
                            <td colspan="2" class="font-extrabold text-slate-800">{{ __('reports.income_statement.section_revenue') }}</td>
                        </tr>
                        @forelse ($data['revenue_lines'] as $line)
                            <tr>
                                <td class="pl-6 text-slate-700">
                                    <span class="font-semibold text-slate-800">{{ $line['kode'] }}</span>
                                    {{ $line['nama'] }}
                                </td>
                                <td class="text-right tabular-nums font-medium text-emerald-700">{{ FormatHelper::rupiah($line['amount']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="pl-6 text-slate-400" colspan="2">{{ __('reports.income_statement.empty_revenue') }}</td>
                            </tr>
                        @endforelse
                        <tr class="font-semibold text-slate-800">
                            <td class="pl-4">{{ __('reports.income_statement.total_revenue') }}</td>
                            <td class="text-right tabular-nums text-emerald-700">{{ FormatHelper::rupiah($data['sales']) }}</td>
                        </tr>

                        <tr class="bg-slate-50/90">
                            <td colspan="2" class="!pt-5 font-extrabold text-slate-800">{{ __('reports.income_statement.section_cogs') }}</td>
                        </tr>
                        <tr>
                            <td class="pl-6 text-slate-700">5-110 — Cost of Goods Sold</td>
                            <td class="text-right tabular-nums {{ $data['cogs'] > 0 ? 'text-slate-800' : 'text-slate-400' }}">
                                {{ $data['cogs'] > 0 ? FormatHelper::rupiah($data['cogs']) : '—' }}
                            </td>
                        </tr>
                        <tr class="bg-amber-50/80 font-extrabold text-amber-950">
                            <td>{{ __('reports.income_statement.section_gross') }}</td>
                            <td class="text-right tabular-nums">{{ FormatHelper::rupiah($data['gross_profit']) }}</td>
                        </tr>

                        <tr class="bg-slate-50/90">
                            <td colspan="2" class="!pt-5 font-extrabold text-slate-800">{{ __('reports.income_statement.section_operating') }}</td>
                        </tr>
                        @forelse ($data['operating_expense_lines'] as $line)
                            <tr>
                                <td class="pl-6 text-slate-700">
                                    <span class="font-semibold text-slate-800">{{ $line['kode'] }}</span>
                                    {{ $line['nama'] }}
                                </td>
                                <td class="text-right tabular-nums font-medium">{{ FormatHelper::rupiah($line['amount']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="pl-6 text-slate-400" colspan="2">{{ __('reports.income_statement.empty_operating') }}</td>
                            </tr>
                        @endforelse
                        <tr class="font-semibold text-slate-800">
                            <td class="pl-4">{{ __('reports.income_statement.total_operating') }}</td>
                            <td class="text-right tabular-nums">{{ FormatHelper::rupiah($data['total_operating_expenses']) }}</td>
                        </tr>

                        <tr class="font-semibold text-slate-700">
                            <td>{{ __('reports.income_statement.before_tax') }}</td>
                            <td class="text-right tabular-nums">{{ FormatHelper::rupiah($data['income_before_tax']) }}</td>
                        </tr>
                        <tr>
                            <td class="pl-6 text-slate-700">5-190 — Income Tax</td>
                            <td class="text-right tabular-nums {{ $data['tax'] > 0 ? 'text-slate-800' : 'text-slate-400' }}">
                                {{ $data['tax'] > 0 ? FormatHelper::rupiah($data['tax']) : '—' }}
                            </td>
                        </tr>
                        <tr class="bg-amber-50 font-extrabold text-amber-950">
                            <td>{{ __('reports.income_statement.section_net') }}</td>
                            <td class="text-right tabular-nums">{{ FormatHelper::rupiah($data['net_profit']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-4 text-xs text-slate-500">
                {{ __('reports.income_statement.footnote') }}
            </p>
        </div>
    </div>
</div>
@endsection
