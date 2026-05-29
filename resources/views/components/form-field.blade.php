@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'helper' => null,
    'placeholder' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'autofocus' => false,
    'disabled' => false,
    'readonly' => false,
    'options' => [],
])

@php
    $error = $errors->first($name);
    $inputId = 'field-' . preg_replace('/[^a-z0-9_-]/i', '-', $name);
    $oldValue = old($name, $value);
    $titleCaseFields = ['nama', 'product_name', 'kat', 'desk', 'deskripsi'];
    $useTitleCase = in_array($name, $titleCaseFields, true);
@endphp

<div {{ $attributes->merge(['class' => 'bakery-field']) }}>
    <label for="{{ $inputId }}" class="mb-1.5 block text-xs font-bold text-slate-600">
        {{ $label }}
        @if ($required)
            <span class="text-rose-500" aria-hidden="true">*</span>
        @endif
    </label>

    @if ($type === 'select')
        <select
            id="{{ $inputId }}"
            name="{{ $name }}"
            @required($required)
            @if ($autofocus) autofocus @endif
            class="bakery-input {{ $error ? '!ring-2 !ring-rose-400' : '' }}"
        >
            {{ $slot }}
        </select>
    @elseif ($type === 'textarea')
        <textarea
            id="{{ $inputId }}"
            name="{{ $name }}"
            rows="{{ $attributes->get('rows', 3) }}"
            placeholder="{{ $placeholder }}"
            @required($required)
            @if ($autofocus) autofocus @endif
            @if ($useTitleCase) data-title-case @endif
            class="bakery-input min-h-[88px] resize-y {{ $error ? '!ring-2 !ring-rose-400' : '' }}"
        >{{ $oldValue }}</textarea>
    @else
        <input
            id="{{ $inputId }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ is_scalar($oldValue) ? $oldValue : '' }}"
            placeholder="{{ $placeholder }}"
            @required($required)
            @if ($min !== null) min="{{ $min }}" @endif
            @if ($max !== null) max="{{ $max }}" @endif
            @if ($step !== null) step="{{ $step }}" @endif
            @if ($autofocus) autofocus @endif
            @if ($disabled) disabled @endif
            @if ($readonly) readonly @endif
            @if ($useTitleCase && $type === 'text') data-title-case @endif
            {{ $attributes->except('class')->merge(['class' => 'bakery-input '.($error ? '!ring-2 !ring-rose-400' : '')]) }}
        />
    @endif

    @if ($helper)
        <p class="mt-1.5 text-xs font-semibold text-slate-400">{{ $helper }}</p>
    @endif

    @if ($error)
        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $error }}</p>
    @endif
</div>
