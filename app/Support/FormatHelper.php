<?php

namespace App\Support;

class FormatHelper
{
    public static function rupiah(int|float $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }

    public static function dateId(?string $date): string
    {
        if (! $date) {
            return '-';
        }

        return \Carbon\Carbon::parse($date)->locale(app()->getLocale())->translatedFormat('d/m/Y');
    }

    public static function titleCase(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        return mb_convert_case(mb_strtolower(trim($value), 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }

    /** @param  array<string, mixed>  $data */
    public static function applyTitleCase(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = static::titleCase($data[$field]);
            }
        }

        return $data;
    }

    public static function formatQtyOne(?float $value): string
    {
        if ($value === null) {
            return '0';
        }

        $n = round((float) $value, 1);

        if (floor($n) === $n) {
            return number_format($n, 0, ',', '.');
        }

        return rtrim(rtrim(number_format($n, 1, ',', '.'), '0'), ',');
    }

    /** @return int|float|null */
    public static function normalizeQtyOne(mixed $value): int|float|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        $n = round((float) $value, 1);

        return floor($n) === $n ? (int) $n : $n;
    }

    public static function formatQtyInput(mixed $value): string
    {
        $normalized = static::normalizeQtyOne($value);

        if ($normalized === null) {
            return '';
        }

        return is_int($normalized) ? (string) $normalized : number_format($normalized, 1, '.', '');
    }

    public static function normalizeInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) round((float) str_replace(',', '.', (string) $value));
    }

    public static function formatIntegerInput(mixed $value): string
    {
        $normalized = static::normalizeInteger($value);

        return $normalized === null ? '' : (string) $normalized;
    }
}
