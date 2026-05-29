@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.tb';
    $pageTitle = __('nav.trial_balance');
    $pageSubtitle = __('page.tb_subtitle');
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-ghost whitespace-nowrap" data-print>{{ __('page.print') }}</button>
@endpush

@section('content')
<div class="bakery-card">
    <div class="bakery-card-body bakery-table-wrap">
        <table class="bakery-table">
            <thead><tr><th>Kode</th><th>Nama</th><th>Debit</th><th>Kredit</th></tr></thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td>{{ $row['account']->kode }}</td>
                        <td>{{ $row['account']->nama }}</td>
                        <td>{{ FormatHelper::rupiah($row['debit']) }}</td>
                        <td>{{ FormatHelper::rupiah($row['kredit']) }}</td>
                    </tr>
                @endforeach
                <tr class="bg-amber-50 font-extrabold">
                    <td colspan="2">{{ __('page.total') }}</td>
                    <td>{{ FormatHelper::rupiah($totalDebit) }}</td>
                    <td>{{ FormatHelper::rupiah($totalKredit) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
