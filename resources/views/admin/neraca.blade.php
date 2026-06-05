@extends('layouts.app')

@php
    use App\Support\FormatHelper;
    $role = 'admin';
    $active = 'admin.neraca';
    $pageTitle = __('app.pages.balance_sheet');
    $pageSubtitle = $filterLabel ?? __('app.pages.balance_sheet_subtitle');
    $isBalanced = $data['is_balanced'];
    $view = $view ?? 'ringkasan';
    $tabs = [
        'ringkasan' => __('reports.balance_sheet.tab_summary'),
        'aset' => __('reports.balance_sheet.tab_assets'),
        'pasiva' => __('reports.balance_sheet.tab_liabilities'),
    ];
    $tabUrl = fn (string $key) => route('admin.neraca', ['as_of' => $asOf, 'view' => $key]);
    $accountCount = $data['assets']->sum(fn ($s) => $s['lines']->count())
        + $data['liabilities']->sum(fn ($s) => $s['lines']->count())
        + $data['equity']->sum(fn ($s) => $s['lines']->count());
@endphp

@push('page-actions')
    <x-pdf-print-button
        :route="route('admin.pdf.neraca')"
        :query="['as_of' => $asOf]"
    />
@endpush

@section('content')
<div class="space-y-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <input type="hidden" name="view" value="{{ $view }}" />
        <div>
            <label for="as_of" class="mb-1 block text-xs font-bold text-slate-600">{{ __('reports.as_of') }}</label>
            <input
                type="date"
                id="as_of"
                name="as_of"
                value="{{ $asOf }}"
                class="bakery-input"
                onchange="this.form.submit()"
            />
        </div>
    </form>

    {{-- Persamaan neraca --}}
    <div class="rounded-2xl bg-gradient-to-br from-amber-50 to-white p-4 ring-1 ring-amber-200/50 sm:p-5">
        <p class="text-xs font-bold uppercase tracking-wide text-amber-900/70">{{ __('reports.balance_sheet.equation') }}</p>
        <div class="mt-3 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center sm:justify-center sm:gap-4">
            <div class="rounded-xl bg-white px-4 py-3 text-center shadow-sm ring-1 ring-sky-100 sm:min-w-[140px]">
                <p class="text-[11px] font-semibold text-sky-700">{{ __('reports.balance_sheet.assets') }}</p>
                <p class="mt-0.5 text-base font-extrabold tabular-nums text-sky-900 sm:text-lg">{{ FormatHelper::rupiah($data['total_assets']) }}</p>
            </div>
            <span class="hidden text-2xl font-black text-amber-800/60 sm:block">=</span>
            <span class="text-center text-lg font-black text-amber-800/50 sm:hidden">=</span>
            <div class="flex flex-col items-stretch gap-2 sm:flex-row sm:items-center sm:gap-3">
                <div class="rounded-xl bg-white px-4 py-3 text-center shadow-sm ring-1 ring-slate-100 sm:min-w-[130px]">
                    <p class="text-[11px] font-semibold text-slate-600">{{ __('reports.balance_sheet.liabilities') }}</p>
                    <p class="mt-0.5 text-base font-extrabold tabular-nums text-slate-900">{{ FormatHelper::rupiah($data['total_liabilities']) }}</p>
                </div>
                <span class="text-center text-lg font-bold text-amber-800/50">+</span>
                <div class="rounded-xl bg-white px-4 py-3 text-center shadow-sm ring-1 ring-emerald-100 sm:min-w-[130px]">
                    <p class="text-[11px] font-semibold text-emerald-700">{{ __('reports.balance_sheet.equity') }}</p>
                    <p class="mt-0.5 text-base font-extrabold tabular-nums text-emerald-900">{{ FormatHelper::rupiah($data['total_equity']) }}</p>
                </div>
            </div>
        </div>
        <p class="mt-3 text-center text-xs {{ $isBalanced ? 'font-semibold text-emerald-700' : 'font-semibold text-rose-600' }}">
            @if ($isBalanced)
                {{ __('reports.balance_sheet.balanced_on_date') }}
            @else
                {{ __('reports.balance_sheet.unbalanced_with_diff', ['diff' => FormatHelper::rupiah($data['difference'])]) }}
            @endif
        </p>
    </div>

    {{-- Tab navigasi --}}
    <div class="flex flex-wrap gap-2" role="tablist">
        @foreach ($tabs as $key => $label)
            <a
                href="{{ $tabUrl($key) }}"
                role="tab"
                aria-selected="{{ $view === $key ? 'true' : 'false' }}"
                class="rounded-full px-4 py-2 text-sm font-bold transition {{ $view === $key ? 'bg-amber-500 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-black/5 hover:bg-slate-50' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($view === 'ringkasan')
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="bakery-card p-4 sm:p-5">
                <div class="mb-4 flex items-center justify-between gap-2">
                    <p class="text-sm font-extrabold text-sky-800">{{ __('reports.balance_sheet.asset_composition') }}</p>
                    <p class="text-xs font-semibold text-slate-500">{{ __('reports.balance_sheet.active_accounts', ['count' => $accountCount]) }}</p>
                </div>
                @include('admin.partials.neraca-summary-bars', [
                    'sections' => $data['assets'],
                    'total' => $data['total_assets'],
                    'barClass' => 'bg-sky-500',
                ])
                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                    <span class="text-xs font-bold uppercase text-slate-500">{{ __('reports.balance_sheet.total_assets') }}</span>
                    <span class="text-lg font-extrabold tabular-nums text-sky-800">{{ FormatHelper::rupiah($data['total_assets']) }}</span>
                </div>
            </div>

            <div class="bakery-card p-4 sm:p-5">
                <p class="mb-4 text-sm font-extrabold text-slate-800">{{ __('reports.balance_sheet.liabilities_equity_composition') }}</p>
                @include('admin.partials.neraca-summary-bars', [
                    'sections' => $data['liabilities']->concat($data['equity']),
                    'total' => $data['total_liabilities_equity'],
                    'barClass' => 'bg-emerald-500',
                ])
                <div class="mt-4 space-y-2 border-t border-slate-100 pt-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">{{ __('reports.balance_sheet.liabilities') }}</span>
                        <span class="font-bold tabular-nums">{{ FormatHelper::rupiah($data['total_liabilities']) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">{{ __('reports.balance_sheet.equity') }}</span>
                        <span class="font-bold tabular-nums text-emerald-700">{{ FormatHelper::rupiah($data['total_equity']) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-100 pt-2 font-extrabold text-amber-950">
                        <span>{{ __('reports.balance_sheet.total_passiva') }}</span>
                        <span class="tabular-nums">{{ FormatHelper::rupiah($data['total_liabilities_equity']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-xs text-slate-500">
            {{ __('reports.balance_sheet.tab_help') }}
        </p>
    @endif

    @if ($view === 'aset')
        <div class="bakery-card overflow-hidden">
            <div class="border-b border-slate-100 bg-sky-50/80 px-4 py-3 sm:px-5">
                <p class="font-extrabold text-sky-900">{{ __('reports.balance_sheet.asset_detail') }}</p>
                <p class="text-xs text-sky-800/70">{{ __('reports.balance_sheet.detail_click_hint', ['date' => FormatHelper::dateId($asOf)]) }}</p>
            </div>
            <div class="space-y-2 p-3 sm:p-4">
                @forelse ($data['assets'] as $section)
                    @include('admin.partials.neraca-section', [
                        'section' => $section,
                        'openFirst' => $loop->first,
                    ])
                @empty
                    <div class="rounded-2xl bg-slate-50 px-6 py-12 text-center">
                        <p class="font-semibold text-slate-600">{{ __('reports.balance_sheet.no_asset_balance') }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ __('reports.balance_sheet.try_other_date') }}</p>
                    </div>
                @endforelse
            </div>
            <div class="border-t border-slate-100 bg-amber-50/60 px-4 py-3 sm:px-5">
                <div class="flex justify-between font-extrabold text-amber-950">
                    <span>{{ __('reports.balance_sheet.total_assets') }}</span>
                    <span class="tabular-nums">{{ FormatHelper::rupiah($data['total_assets']) }}</span>
                </div>
            </div>
        </div>
    @endif

    @if ($view === 'pasiva')
        <div class="space-y-4">
            <div class="bakery-card overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50 px-4 py-3 sm:px-5">
                    <p class="font-extrabold text-slate-800">{{ __('reports.balance_sheet.liabilities') }}</p>
                    <p class="text-xs text-slate-500">{{ __('reports.balance_sheet.liabilities_short_desc') }}</p>
                </div>
                <div class="space-y-2 p-3 sm:p-4">
                    @forelse ($data['liabilities'] as $section)
                        @include('admin.partials.neraca-section', [
                            'section' => $section,
                            'openFirst' => $loop->first,
                        ])
                    @empty
                        <p class="rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-400">{{ __('reports.balance_sheet.no_liability_balance') }}</p>
                    @endforelse
                </div>
                <div class="border-t border-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 sm:px-5">
                    <span class="flex justify-between tabular-nums">
                        <span>{{ __('reports.balance_sheet.subtotal_liabilities') }}</span>
                        <span>{{ FormatHelper::rupiah($data['total_liabilities']) }}</span>
                    </span>
                </div>
            </div>

            <div class="bakery-card overflow-hidden">
                <div class="border-b border-slate-100 bg-emerald-50/80 px-4 py-3 sm:px-5">
                    <p class="font-extrabold text-emerald-900">{{ __('reports.balance_sheet.equity') }}</p>
                    <p class="text-xs text-emerald-800/70">{{ __('reports.balance_sheet.equity_retained') }}</p>
                </div>
                <div class="space-y-2 p-3 sm:p-4">
                    @forelse ($data['equity'] as $section)
                        @include('admin.partials.neraca-section', [
                            'section' => $section,
                            'openFirst' => $loop->first,
                        ])
                    @empty
                        <p class="rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-400">{{ __('reports.balance_sheet.no_equity_balance') }}</p>
                    @endforelse
                </div>
                <div class="border-t border-amber-200/60 bg-amber-50/60 px-4 py-3 sm:px-5">
                    <div class="flex justify-between text-sm font-bold text-slate-700">
                        <span>{{ __('reports.balance_sheet.total_equity_label') }}</span>
                        <span class="tabular-nums">{{ FormatHelper::rupiah($data['total_equity']) }}</span>
                    </div>
                    <div class="mt-2 flex justify-between font-extrabold text-amber-950">
                        <span>{{ __('reports.balance_sheet.liabilities_plus_equity') }}</span>
                        <span class="tabular-nums">{{ FormatHelper::rupiah($data['total_liabilities_equity']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
