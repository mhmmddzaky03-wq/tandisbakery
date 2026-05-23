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
}
