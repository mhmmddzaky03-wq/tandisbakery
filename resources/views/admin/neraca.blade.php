@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.neraca';
    $pageTitle = 'Neraca Keuangan';
    $pageSubtitle = 'Neraca Keuangan Tandi\'s Bakery';
@endphp

@push('page-actions')
    <button type="button" class="bakery-btn-ghost whitespace-nowrap" data-print>Cetak</button>
@endpush

@section('content')
<div class="bakery-card">
    <div class="bakery-card-body">
        <p class="mb-2">Total Aset: <strong>{{ FormatHelper::rupiah($data['total_assets']) }}</strong></p>
        <p class="mb-4">Liabilitas + Ekuitas: <strong>{{ FormatHelper::rupiah($data['total_liabilities'] + $data['total_equity']) }}</strong></p>
        @foreach ($data['sections'] as $group => $accounts)
            @if ($accounts->isNotEmpty())
                <h3 class="mb-2 mt-4 font-bold">{{ $group }}</h3>
                <table class="bakery-table mb-4">
                    <tbody>
                        @foreach ($accounts as $row)
                            <tr>
                                <td>{{ $row['account']->kode }}</td>
                                <td>{{ $row['account']->nama }}</td>
                                <td class="text-right font-bold">{{ FormatHelper::rupiah($row['saldo']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    </div>
</div>
@endsection
