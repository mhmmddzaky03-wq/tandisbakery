@extends('layouts.app')
@php use App\Support\FormatHelper; $role='admin'; $active='admin.neraca'; $pageTitle=__('nav.balance_sheet'); $subtitle=__('nav.financial_reports'); @endphp
@section('content')
<div class="pt-6 bakery-card p-6">
    <div class="flex justify-between mb-4"><div class="text-lg font-extrabold">{{ __('nav.balance_sheet') }}</div><button type="button" class="bakery-btn-ghost" data-print>Cetak</button></div>
    <p class="mb-2">Total Aset: <strong>{{ FormatHelper::rupiah($data['total_assets']) }}</strong></p>
    <p class="mb-4">Liabilitas + Ekuitas: <strong>{{ FormatHelper::rupiah($data['total_liabilities'] + $data['total_equity']) }}</strong></p>
    @foreach ($data['sections'] as $group => $accounts)
        @if ($accounts->isNotEmpty())
            <h3 class="font-bold mt-4 mb-2">{{ $group }}</h3>
            <table class="bakery-table mb-4"><tbody>
                @foreach ($accounts as $row)
                    <tr><td>{{ $row['account']->kode }}</td><td>{{ $row['account']->nama }}</td><td class="text-right font-bold">{{ FormatHelper::rupiah($row['saldo']) }}</td></tr>
                @endforeach
            </tbody></table>
        @endif
    @endforeach
</div>
@endsection
