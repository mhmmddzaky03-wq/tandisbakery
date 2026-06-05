@props([
    'units' => [],
    'selected' => '',
    'name' => null,
    'disabled' => false,
    'compact' => false,
])

@php
    $selected = (string) ($selected ?? '');
@endphp

<div
    {{ $attributes->merge(['class' => 'unit-toggle'.($compact ? ' unit-toggle--compact' : '')]) }}
    data-unit-toggle
    data-selected="{{ $selected }}"
    role="group"
    aria-label="{{ __('app.common.select_unit') }}"
    @if ($disabled) data-unit-toggle-disabled @endif
>
    @foreach ($units as $unit)
        @php
            $value = is_array($unit) ? ($unit['value'] ?? '') : $unit;
            $label = is_array($unit) ? ($unit['label'] ?? $value) : $unit;
            $isActive = $value === $selected;
        @endphp
        <button
            type="button"
            class="unit-toggle-btn{{ $isActive ? ' unit-toggle-btn--active' : '' }}"
            data-unit-value="{{ $value }}"
            aria-pressed="{{ $isActive ? 'true' : 'false' }}"
            @if ($disabled) disabled @endif
        >{{ $label }}</button>
    @endforeach
    @if ($name)
        <input type="hidden" name="{{ $name }}" value="{{ $selected }}" data-unit-toggle-input />
    @endif
</div>
