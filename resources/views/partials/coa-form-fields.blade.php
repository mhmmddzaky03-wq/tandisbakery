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
    <x-form-field :label="__('coa.field_code')" name="kode" :value="old('kode', $codeValue)" required autofocus placeholder="1-110" />
@endif

<x-form-field :label="__('coa.field_name')" name="nama" :value="old('nama', $nama ?? '')" required :autofocus="! $showCode" />

<x-form-field :label="__('coa.field_position')" name="posisi" type="select" required :helper="__('coa.position_hint')">
    <option value="" disabled @selected($selectedPosisi === '')>{{ __('coa.select_position') }}</option>
    <option value="Debit" @selected($selectedPosisi === 'Debit')>{{ __('coa.debit') }}</option>
    <option value="Credit" @selected($selectedPosisi === 'Credit')>{{ __('coa.credit') }}</option>
</x-form-field>

<x-form-field :label="__('coa.field_group')" name="grup" type="select" required>
    <option value="" disabled @selected($selectedGrup === '' || $selectedGrup === null)>{{ __('coa.select_group') }}</option>
    @foreach (array_keys($coaGroupMap) as $grup)
        <option value="{{ $grup }}" @selected($selectedGrup === $grup)>{{ $grup }}</option>
    @endforeach
</x-form-field>

<x-form-field :label="__('coa.field_subgroup')" name="sub_grup" type="select" required :disabled="! $selectedGrup">
    <option value="" disabled @selected($selectedSubGrup === '' || $selectedSubGrup === null)>{{ __('coa.select_subgroup') }}</option>
    @if ($selectedGrup && isset($coaGroupMap[$selectedGrup]))
        @foreach ($coaGroupMap[$selectedGrup] as $sub)
            <option value="{{ $sub }}" @selected($selectedSubGrup === $sub)>{{ $sub }}</option>
        @endforeach
    @endif
</x-form-field>
