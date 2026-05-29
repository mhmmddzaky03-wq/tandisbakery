@props([
    'coaGroupMap' => [],
    'selectedGrup' => null,
    'selectedSubGrup' => null,
    'showCode' => false,
    'codeValue' => null,
])

@php
    $selectedGrup = old('grup', $selectedGrup);
    $selectedSubGrup = old('sub_grup', $selectedSubGrup);
    $selectedPosisi = old('posisi', $posisi ?? '');
@endphp

@if ($showCode)
    <x-form-field label="Kode" name="kode" :value="old('kode', $codeValue)" required autofocus placeholder="1-110" />
@endif

<x-form-field label="Nama Akun" name="nama" :value="old('nama', $nama ?? '')" required :autofocus="! $showCode" />

<x-form-field label="Posisi" name="posisi" type="select" required helper="Saldo normal akun bertambah di sisi mana">
    <option value="" disabled @selected($selectedPosisi === '')>Pilih posisi</option>
    <option value="Debit" @selected($selectedPosisi === 'Debit')>Debit (Dr)</option>
    <option value="Credit" @selected($selectedPosisi === 'Credit')>Kredit (Cr)</option>
</x-form-field>

<x-form-field label="Grup" name="grup" type="select" required>
    <option value="" disabled @selected($selectedGrup === '' || $selectedGrup === null)>Pilih grup</option>
    @foreach (array_keys($coaGroupMap) as $grup)
        <option value="{{ $grup }}" @selected($selectedGrup === $grup)>{{ $grup }}</option>
    @endforeach
</x-form-field>

<x-form-field label="Sub-Grup" name="sub_grup" type="select" required :disabled="! $selectedGrup">
    <option value="" disabled @selected($selectedSubGrup === '' || $selectedSubGrup === null)>Pilih sub-grup</option>
    @if ($selectedGrup && isset($coaGroupMap[$selectedGrup]))
        @foreach ($coaGroupMap[$selectedGrup] as $sub)
            <option value="{{ $sub }}" @selected($selectedSubGrup === $sub)>{{ $sub }}</option>
        @endforeach
    @endif
</x-form-field>
