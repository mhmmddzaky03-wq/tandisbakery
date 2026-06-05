@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.laporan_penjualan';
    $pageTitle = __('app.pages.sales_report');
    $pageSubtitle = $filterLabel ?? __('app.pages.sales_report_subtitle');
@endphp

@push('page-actions')
    <x-pdf-print-button
        :route="route('admin.pdf.penjualan')"
        :query="array_filter(request()->only(['from', 'to']), fn ($v) => $v !== null && $v !== '')"
    />
@endpush

@section('content')
<div class="space-y-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label for="from" class="mb-1 block text-xs font-bold text-slate-600">{{ __('app.common.from') }}</label>
            <input type="date" id="from" name="from" value="{{ $from ?? '' }}" class="bakery-input" />
        </div>
        <div>
            <label for="to" class="mb-1 block text-xs font-bold text-slate-600">{{ __('app.common.to') }}</label>
            <input type="date" id="to" name="to" value="{{ $to ?? '' }}" class="bakery-input" />
        </div>
        <button type="submit" class="bakery-btn-primary shrink-0">{{ __('reports.show') }}</button>
    </form>

    <div class="bakery-card overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50 px-4 py-3 sm:px-6">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ __('reports.sales_report.title') }}</p>
            <p class="mt-0.5 text-lg font-extrabold text-emerald-700">{{ FormatHelper::rupiah($total) }}</p>
            <p class="text-xs text-slate-500">{{ __('reports.transactions_count', ['count' => $sales->count()]) }} · {{ $filterLabel }}</p>
        </div>
        <div class="bakery-card-body bakery-table-wrap">
            <table class="bakery-table text-sm">
                <thead>
                    <tr>
                        <th>{{ __('app.common.id') }}</th>
                        <th>{{ __('app.common.date') }}</th>
                        <th class="text-right">{{ __('app.common.total') }}</th>
                        <th>{{ __('reports.method') }}</th>
                        <th class="text-center">{{ __('app.common.quantity') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $s)
                        <tr>
                            <td class="font-bold">{{ $s->id }}</td>
                            <td>{{ FormatHelper::dateId($s->tanggal) }}</td>
                            <td class="text-right font-medium tabular-nums">{{ FormatHelper::rupiah($s->total) }}</td>
                            <td>{{ $s->metode }}</td>
                            <td class="text-center">{{ $s->jumlah }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">{{ __('reports.not_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
