@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.laporan_penjualan';
    $pageTitle = __('nav.sales_report');
    $pageSubtitle = __('page.sales_report_subtitle');
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-ghost whitespace-nowrap" data-print>{{ __('page.print') }}</button>
@endpush

@section('content')
<div class="bakery-card">
    <div class="bakery-card-body">
        <p class="mb-4 font-extrabold text-emerald-600">Total: {{ FormatHelper::rupiah($total) }}</p>
        <div class="bakery-table-wrap">
            <table class="bakery-table">
                <thead><tr><th>ID</th><th>Tanggal</th><th>Total</th><th>Metode</th></tr></thead>
                <tbody>
                    @foreach ($sales as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>{{ FormatHelper::dateId($s->tanggal) }}</td>
                            <td>{{ FormatHelper::rupiah($s->total) }}</td>
                            <td>{{ $s->metode }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
