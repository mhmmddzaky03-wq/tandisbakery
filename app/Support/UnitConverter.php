<?php

namespace App\Support;

use App\Models\RawMaterial;

class UnitConverter
{
    /** @var array<string, string> */
    private const ALIASES = [
        'kilogram' => 'kg',
        'kg' => 'kg',
        'gram' => 'gram',
        'g' => 'gram',
        'liter' => 'L',
        'l' => 'L',
        'ml' => 'ml',
        'mililiter' => 'ml',
        'milliliter' => 'ml',
    ];

    /** @var array<string, array<string, float>> */
    private const FAMILIES = [
        'mass' => [
            'kg' => 1.0,
            'gram' => 0.001,
        ],
        'volume' => [
            'L' => 1.0,
            'ml' => 0.001,
        ],
    ];

    /** @var array<string, string> */
    private const SHORT_LABELS = [
        'kg' => 'kg',
        'gram' => 'gr',
        'L' => 'L',
        'ml' => 'mL',
    ];

    public static function resolve(?string $unit): ?string
    {
        if ($unit === null || trim($unit) === '') {
            return null;
        }

        $key = mb_strtolower(trim($unit), 'UTF-8');

        return self::ALIASES[$key] ?? null;
    }

    /** @return array<int, string> */
    public static function alternatives(?string $unit): array
    {
        $resolved = self::resolve($unit);
        if ($resolved === null) {
            return $unit ? [trim($unit)] : [];
        }

        foreach (self::FAMILIES as $units) {
            if (array_key_exists($resolved, $units)) {
                return array_keys($units);
            }
        }

        return [$resolved];
    }

    public static function label(string $unit): string
    {
        $resolved = self::resolve($unit) ?? $unit;

        return self::SHORT_LABELS[$resolved] ?? ucfirst($resolved);
    }

    public static function shortLabel(string $unit): string
    {
        return self::label($unit);
    }

    public static function canConvert(?string $from, ?string $to): bool
    {
        $fromResolved = self::resolve($from);
        $toResolved = self::resolve($to);

        if ($fromResolved === null || $toResolved === null) {
            return $fromResolved === $toResolved;
        }

        if ($fromResolved === $toResolved) {
            return true;
        }

        return self::family($fromResolved) !== null
            && self::family($fromResolved) === self::family($toResolved);
    }

    public static function convert(float $qty, ?string $from, ?string $to): ?float
    {
        $fromResolved = self::resolve($from);
        $toResolved = self::resolve($to);

        if ($fromResolved === null || $toResolved === null) {
            return $fromResolved === $toResolved ? $qty : null;
        }

        if ($fromResolved === $toResolved) {
            return $qty;
        }

        $family = self::family($fromResolved);
        if ($family === null || $family !== self::family($toResolved)) {
            return null;
        }

        $units = self::FAMILIES[$family];
        $factor = $units[$fromResolved] / $units[$toResolved];
        $result = $qty * $factor;

        return self::roundConverted($result, $toResolved);
    }

    public static function roundConverted(float $value, ?string $unit): float
    {
        $resolved = self::resolve($unit);

        if ($resolved === 'gram' || $resolved === 'ml') {
            $rounded = round($value, 4);
            if (abs($rounded - round($rounded)) < 0.000_01) {
                return (float) round($rounded);
            }

            return round($rounded, 1);
        }

        $rounded = round($value, 6);

        if (abs($rounded) < 1 && abs($rounded) > 0) {
            return round($rounded, 4);
        }

        $oneDecimal = round($rounded, 1);

        return floor($oneDecimal) === $oneDecimal ? (float) (int) $oneDecimal : $oneDecimal;
    }

    public static function convertMaterial(RawMaterial $material, string $targetUnit): void
    {
        $from = self::resolve($material->satuan);
        $to = self::resolve($targetUnit);

        if ($from === $to) {
            if ($to !== null) {
                $material->satuan = $to;
            }

            return;
        }

        if (! self::canConvert($material->satuan, $targetUnit)) {
            return;
        }

        $family = self::family($from);
        $units = self::FAMILIES[$family];
        $qtyFactor = $units[$from] / $units[$to];

        $material->jumlah = self::roundConverted((float) $material->jumlah * $qtyFactor, $to);
        $material->min = self::roundConverted((float) $material->min * $qtyFactor, $to);
        $material->harga = max(1, (int) round((int) $material->harga / $qtyFactor));
        $material->satuan = $to;
    }

    private static function family(string $resolvedUnit): ?string
    {
        foreach (self::FAMILIES as $family => $units) {
            if (array_key_exists($resolvedUnit, $units)) {
                return $family;
            }
        }

        return null;
    }
}
