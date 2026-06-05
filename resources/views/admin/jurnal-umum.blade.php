@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.jurnal';
    $pageTitle = __('app.pages.journal');
    $pageSubtitle = __('app.pages.journal_subtitle');
    $totalDebit = $totals['debit'];
    $totalKredit = $totals['kredit'];
    $totalTransaksi = $totals['transaksi'];
    $isBalanced = $totalDebit === $totalKredit;

    $sourceBadgeClass = fn (string $tone) => match ($tone) {
        'emerald' => 'bg-emerald-50 text-emerald-800 ring-emerald-200/80',
        'sky' => 'bg-sky-50 text-sky-800 ring-sky-200/80',
        'violet' => 'bg-violet-50 text-violet-800 ring-violet-200/80',
        'amber' => 'bg-amber-50 text-amber-900 ring-amber-200/80',
        default => 'bg-slate-100 text-slate-700 ring-slate-200/80',
    };
@endphp

@push('page-actions')
    <x-pdf-print-button
        :route="route('admin.pdf.jurnal')"
        :query="array_filter(['source' => $source ?? null, 'from' => $from ?? null, 'to' => $to ?? null], fn ($v) => $v !== null && $v !== '')"
    />
@endpush

@section('content')
<div class="space-y-4">
    <form method="GET" id="jurnal-filter-form" class="flex flex-wrap items-end gap-3">
        <div class="min-w-[min(100%,200px)]">
            <label for="source" class="mb-1 block text-xs font-bold text-slate-600">{{ __('reports.journal.source') }}</label>
            <select id="source" name="source" class="bakery-input w-full min-w-[180px]" onchange="this.form.submit()">
                @foreach ($sourceOptions as $value => $label)
                    <option value="{{ $value }}" @selected(($source ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="from" class="mb-1 block text-xs font-bold text-slate-600">{{ __('app.common.from') }}</label>
            <input type="date" id="from" name="from" value="{{ $from ?? '' }}" class="bakery-input" onchange="this.form.submit()" />
        </div>
        <div>
            <label for="to" class="mb-1 block text-xs font-bold text-slate-600">{{ __('app.common.to') }}</label>
            <input type="date" id="to" name="to" value="{{ $to ?? '' }}" class="bakery-input" onchange="this.form.submit()" />
        </div>
    </form>

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-4 ring-1 ring-black/5">
            <p class="text-xs font-semibold text-slate-500">{{ __('reports.journal.total_transactions') }}</p>
            <p class="mt-1 text-lg font-extrabold text-slate-800">{{ number_format($totalTransaksi, 0, ',', '.') }}</p>
            <p class="mt-0.5 text-xs text-slate-400">
                @if ($from || $to || ($source ?? '') !== '')
                    {{ __('reports.journal.filter_active') }}
                @else
                    {{ __('reports.journal.all_periods_sources') }}
                @endif
            </p>
        </div>
        <div class="rounded-2xl bg-white p-4 ring-1 ring-black/5">
            <p class="text-xs font-semibold text-slate-500">{{ __('reports.journal.total_debit_label') }}</p>
            <p class="mt-1 text-lg font-extrabold tabular-nums text-slate-800">{{ FormatHelper::rupiah($totalDebit) }}</p>
        </div>
        <div class="rounded-2xl p-4 ring-1 {{ $isBalanced ? 'bg-emerald-50 ring-emerald-200/60' : 'bg-rose-50 ring-rose-200/60' }}">
            <p class="text-xs font-semibold {{ $isBalanced ? 'text-emerald-800/80' : 'text-rose-800/80' }}">{{ __('reports.journal.total_credit_label') }}</p>
            <p class="mt-1 text-lg font-extrabold tabular-nums {{ $isBalanced ? 'text-emerald-900' : 'text-rose-900' }}">
                {{ FormatHelper::rupiah($totalKredit) }}
            </p>
            <p class="mt-0.5 text-xs {{ $isBalanced ? 'text-emerald-700/70' : 'text-rose-700/70' }}">
                {{ $isBalanced ? __('reports.journal.balanced') : __('reports.journal.unbalanced') }}
            </p>
        </div>
    </div>

    <div class="bakery-card overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50 px-4 py-3 sm:px-6">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ __('reports.journal.list_title') }}</p>
            <p class="mt-0.5 text-sm text-slate-600">
                {{ __('reports.journal.list_filter_help') }}
            </p>
        </div>

        <div class="bakery-card-body space-y-5">
            @forelse ($journals as $day)
                <section class="overflow-hidden rounded-2xl ring-1 ring-slate-100">
                    <div class="flex flex-wrap items-center justify-between gap-2 bg-amber-50 px-4 py-3 sm:px-5">
                        <div>
                            <p class="font-extrabold text-amber-950">{{ $day['hari'] }}</p>
                            <p class="text-sm font-semibold text-amber-900/80">{{ $day['tanggal'] }}</p>
                        </div>
                        <p class="text-xs font-semibold text-amber-800/70 tabular-nums">
                            D {{ FormatHelper::rupiah($day['total_debit']) }}
                            · K {{ FormatHelper::rupiah($day['total_kredit']) }}
                        </p>
                    </div>

                    @foreach ($day['transactions'] as $tx)
                        <div class="{{ ! $loop->last ? 'border-b border-slate-100' : '' }}">
                            <div class="flex flex-wrap items-start justify-between gap-3 bg-slate-50/80 px-4 py-3 sm:px-5">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold ring-1 {{ $sourceBadgeClass($tx['source']['tone']) }}">
                                            {{ $tx['source']['label'] }}
                                        </span>
                                        @if ($tx['ref'])
                                            <span class="font-bold text-slate-800">{{ $tx['ref'] }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-slate-700">{{ $tx['deskripsi'] }}</p>
                                </div>
                                @if ($tx['can_delete'])
                                    <form
                                        method="POST"
                                        action="{{ route('admin.jurnal.destroy', $tx['id']) }}"
                                        class="shrink-0"
                                        onsubmit="return window.confirm(@js(__('reports.journal.confirm_delete')))"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-700">
                                            {{ __('app.common.delete') }}
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="bakery-table-wrap rounded-none ring-0">
                                <table class="bakery-table text-sm">
                                    <thead>
                                        <tr>
                                            <th class="w-[88px]">{{ __('reports.account') }}</th>
                                            <th>{{ __('reports.account_name') }}</th>
                                            <th class="w-[128px] text-right">{{ __('reports.debit') }}</th>
                                            <th class="w-[128px] text-right">{{ __('reports.credit') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tx['entries'] as $e)
                                            <tr>
                                                <td class="font-bold text-slate-800">{{ $e['akun'] }}</td>
                                                <td class="text-slate-600">{{ $e['nama_akun'] }}</td>
                                                <td class="text-right tabular-nums {{ $e['debit'] > 0 ? 'font-medium text-slate-800' : 'text-slate-400' }}">
                                                    {{ $e['debit'] > 0 ? FormatHelper::rupiah($e['debit']) : '—' }}
                                                </td>
                                                <td class="text-right tabular-nums {{ $e['kredit'] > 0 ? 'font-medium text-slate-800' : 'text-slate-400' }}">
                                                    {{ $e['kredit'] > 0 ? FormatHelper::rupiah($e['kredit']) : '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="bg-slate-50/90 font-semibold text-slate-700">
                                            <td colspan="2" class="text-right sm:text-left">{{ __('reports.subtotal') }}</td>
                                            <td class="text-right tabular-nums">{{ FormatHelper::rupiah($tx['total_debit']) }}</td>
                                            <td class="text-right tabular-nums">{{ FormatHelper::rupiah($tx['total_kredit']) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </section>
            @empty
                <div class="rounded-2xl bg-slate-50 px-6 py-14 text-center">
                    <p class="font-semibold text-slate-600">{{ __('reports.not_found') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
