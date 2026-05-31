@props([
    'qty',
    'unit',
    'qtyClass' => 'font-semibold tabular-nums text-slate-800',
    'anchorToggle' => false,
])

@php
    use App\Support\FormatHelper;
    use App\Support\UnitConverter;

    $baseQty = (float) $qty;
    $baseUnit = $unit ?? '';
    $alternatives = UnitConverter::alternatives($baseUnit);
    $hasToggle = count($alternatives) > 1;
    $toggleUnits = collect($alternatives)->map(fn (string $u): array => [
        'value' => $u,
        'label' => UnitConverter::shortLabel($u),
    ])->all();
@endphp

@if ($anchorToggle)
    <span
        {{ $attributes->merge(['class' => 'unit-qty unit-qty--anchor-toggle']) }}
        data-unit-display
        data-base-qty="{{ $baseQty }}"
        data-base-unit="{{ $baseUnit }}"
    >
        <span class="unit-qty__row">
            <span data-unit-display-qty @class([$qtyClass, 'unit-qty__value', 'text-sm sm:text-base'])>{{ FormatHelper::formatQtyForUnit($baseQty, $baseUnit) }}</span>
            <span class="unit-qty__toggle-slot">
                @if ($hasToggle)
                    <x-unit-toggle :units="$toggleUnits" :selected="$baseUnit" compact />
                @else
                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-slate-500">{{ $baseUnit }}</span>
                @endif
            </span>
        </span>
    </span>
@else
    <span
        {{ $attributes->merge(['class' => 'unit-qty inline-flex items-center gap-2']) }}
        data-unit-display
        data-base-qty="{{ $baseQty }}"
        data-base-unit="{{ $baseUnit }}"
    >
        <span data-unit-display-qty @class([$qtyClass, 'text-sm sm:text-base'])>{{ FormatHelper::formatQtyForUnit($baseQty, $baseUnit) }}</span>
        @if ($hasToggle)
            <x-unit-toggle :units="$toggleUnits" :selected="$baseUnit" compact />
        @else
            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-slate-500">{{ $baseUnit }}</span>
        @endif
    </span>
@endif
