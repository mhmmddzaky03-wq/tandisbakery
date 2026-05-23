@extends('layouts.app')
@php use App\Support\FormatHelper; $role='admin'; $active='admin.jurnal'; $pageTitle=__('nav.journal_entries'); $subtitle=__('nav.accounting'); @endphp
@section('content')
<div class="pt-6 bakery-card">
    <div class="bakery-card-header">
        <div>
            <div class="text-lg font-extrabold">{{ __('nav.journal_entries') }}</div>
            <div class="text-sm text-slate-400">{{ $totalTransaksi }} transaksi • D {{ FormatHelper::rupiah($totalDebit) }} / K {{ FormatHelper::rupiah($totalKredit) }}</div>
        </div>
        <div class="flex gap-2">
            <button type="button" class="bakery-btn-ghost" data-print>Cetak</button>
            <button type="button" class="bakery-btn-primary" data-modal-open="jurnal-baru">{{ __('page.add_entry') }}</button>
        </div>
    </div>
    <div class="bakery-card-body">
        <form method="GET" class="mb-5 grid gap-3 sm:grid-cols-4">
            <input class="bakery-input sm:col-span-2" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('page.search_by_ref_account') }}" />
            <input class="bakery-input" type="date" name="from" value="{{ $from ?? '' }}" aria-label="Dari tanggal" />
            <div class="flex gap-2"><input class="bakery-input flex-1" type="date" name="to" value="{{ $to ?? '' }}" aria-label="Sampai tanggal" /><button class="bakery-btn-primary shrink-0">Filter</button></div>
        </form>
        @forelse ($journals as $journal)
            <div class="mb-4 overflow-hidden rounded-2xl ring-1 ring-slate-100">
                <div class="bg-amber-50 px-5 py-3 font-extrabold">{{ $journal['hari'] }}, {{ $journal['tanggal'] }}</div>
                <table class="bakery-table">
                    <thead><tr><th>Ref</th><th>Akun</th><th>Uraian</th><th>Debit</th><th>Kredit</th></tr></thead>
                    <tbody>
                        @foreach ($journal['entries'] as $e)
                            <tr>
                                <td>{{ $e['ref'] ?: '—' }}</td>
                                <td>{{ $e['akun'] }}</td>
                                <td>{{ $e['uraian'] }}</td>
                                <td>{{ $e['debit'] ? FormatHelper::rupiah($e['debit']) : '—' }}</td>
                                <td>{{ $e['kredit'] ? FormatHelper::rupiah($e['kredit']) : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <p class="py-8 text-center text-slate-500">Belum ada jurnal.</p>
        @endforelse
    </div>
</div>

<x-modal id="jurnal-baru" title="Tambah Jurnal" subtitle="Debit dan kredit harus sama (double entry)" size="lg" :auto-open="$errors->has('lines') || $errors->has('deskripsi')">
    <form method="POST" action="{{ route('admin.jurnal.store') }}" data-modal-form data-journal-form>
        @csrf
        <x-form-section title="Informasi transaksi">
            <x-form-field label="Tanggal" name="tanggal" type="date" :value="old('tanggal', date('Y-m-d'))" required autofocus />
            <x-form-field label="Deskripsi" name="deskripsi" :value="old('deskripsi')" required placeholder="Contoh: Restock bahan baku" />
            <x-form-field label="Referensi" name="ref" :value="old('ref')" helper="Opsional — no. invoice / pihak terkait" placeholder="INV-001" />
        </x-form-section>
        <x-form-section title="Pencatatan akun" class="mt-6">
            <x-form-field label="Nominal (Rp)" name="jumlah" type="number" :value="old('jumlah')" min="1" required helper="Nilai yang sama akan dicatat di debit dan kredit" />
            <x-form-field label="Akun debit (bertambah)" name="akun_debit" type="select" required>
                @foreach ($accounts as $a)
                    <option value="{{ $a->kode }}" @selected(old('akun_debit') == $a->kode)>{{ $a->kode }} — {{ $a->nama }}</option>
                @endforeach
            </x-form-field>
            <x-form-field label="Akun kredit (bertambah)" name="akun_kredit" type="select" required>
                @foreach ($accounts as $a)
                    <option value="{{ $a->kode }}" @selected(old('akun_kredit', '1-110') == $a->kode)>{{ $a->kode }} — {{ $a->nama }}</option>
                @endforeach
            </x-form-field>
        </x-form-section>
        @if ($errors->has('lines'))
            <p class="mt-4 text-xs font-semibold text-rose-600" role="alert">{{ $errors->first('lines') }}</p>
        @endif
        <x-form-actions submit="Simpan Jurnal" />
    </form>
</x-modal>
@endsection
