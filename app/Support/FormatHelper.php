<?php

namespace App\Support;

class FormatHelper
{
    public static function rupiah(int|float $amount): string
    {
        return 'Rp'.number_format((float) $amount, 0, ',', '.');
    }

    /** General ledger debit/kredit column (kosong = "-"). */
    public static function glAmount(int $amount): string
    {
        if ($amount === 0) {
            return '-';
        }

        return 'Rp'.number_format($amount, 0, ',', '.');
    }

    /** General ledger saldo berjalan (nol = "Rp-", negatif dalam kurung). */
    public static function glBalance(int $amount): string
    {
        if ($amount === 0) {
            return 'Rp-';
        }

        if ($amount < 0) {
            return 'Rp('.number_format(abs($amount), 0, ',', '.').')';
        }

        return 'Rp'.number_format($amount, 0, ',', '.');
    }

    /** Tanggal buku besar ala Excel: 1-Jun */
    public static function dateGl(?string $date): string
    {
        if (! $date) {
            return '-';
        }

        return \Carbon\Carbon::parse($date)->locale('en')->translatedFormat('j-M');
    }

    /** Trial balance / FS: negative amounts in parentheses. */
    public static function rupiahTb(int $amount): string
    {
        if ($amount < 0) {
            return '('.number_format(abs($amount), 0, ',', '.').')';
        }

        if ($amount === 0) {
            return '-';
        }

        return number_format($amount, 0, ',', '.');
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
        return static::formatQtyForUnit($value, null);
    }

    public static function formatQtyForUnit(?float $value, ?string $unit): string
    {
        if ($value === null) {
            return '0';
        }

        $resolved = $unit ? UnitConverter::resolve($unit) : null;
        $n = UnitConverter::roundConverted((float) $value, $resolved ?? $unit);

        if ($n === 0.0) {
            return '0';
        }

        if (floor($n) === $n && abs($n) >= 1) {
            return number_format((int) $n, 0, ',', '.');
        }

        $decimals = match (true) {
            in_array($resolved, ['gram', 'ml'], true) => 1,
            abs($n) < 1 => 4,
            default => 1,
        };

        return rtrim(rtrim(number_format($n, $decimals, ',', '.'), '0'), ',');
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
