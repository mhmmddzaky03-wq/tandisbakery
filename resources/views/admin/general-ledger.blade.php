@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.gl';
    $pageTitle = __('nav.general_ledger');
    $pageSubtitle = __('page.gl_subtitle');
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-ghost whitespace-nowrap" data-print>{{ __('page.print') }}</button>
@endpush

@section('content')
<div class="bakery-card">
    <div class="bakery-card-body">
        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            <select name="account" class="bakery-input">
                @foreach ($accounts as $a)
                    <option value="{{ $a->kode }}" @selected($accountKode === $a->kode)>{{ $a->kode }} — {{ $a->nama }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ $from }}" class="bakery-input" />
            <input type="date" name="to" value="{{ $to }}" class="bakery-input" />
            <button type="submit" class="bakery-btn-primary shrink-0">Tampilkan</button>
        </form>
        <p class="mb-4 font-bold">{{ $account->nama }} ({{ $account->kode }}) — Saldo {{ FormatHelper::rupiah($balance) }}</p>
        <div class="bakery-table-wrap">
            <table class="bakery-table">
                <thead><tr><th>No</th><th>Tanggal</th><th>Ref</th><th>Debit</th><th>Kredit</th><th>Saldo</th></tr></thead>
                <tbody>
                    @foreach ($rows as $r)
                        <tr>
                            <td>{{ $r['no'] }}</td>
                            <td>{{ $r['tgl'] }}</td>
                            <td>{{ $r['ref'] }}</td>
                            <td>{{ $r['debit'] ? FormatHelper::rupiah($r['debit']) : '-' }}</td>
                            <td>{{ $r['kredit'] ? FormatHelper::rupiah($r['kredit']) : '-' }}</td>
                            <td>{{ FormatHelper::rupiah($r['saldo']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
