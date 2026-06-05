<?php

namespace App\Support;

class LocaleLabels
{
    public static function stockStatus(float $qty, float $min): string
    {
        if ($qty <= 0) {
            return __('stock.status.out');
        }

        if ($qty <= $min) {
            return __('stock.status.low');
        }

        return __('stock.status.safe');
    }

    public static function productionStatus(?string $status): string
    {
        return match ($status) {
            'Berhasil' => __('production.status.success'),
            'Gagal' => __('production.status.failed'),
            default => $status ?? '',
        };
    }

    public static function paymentMethod(?string $method): string
    {
        return match ($method) {
            'Tunai', 'Cash' => __('sales.payment.cash'),
            'Transfer' => __('sales.payment.transfer'),
            'Campuran', 'Mix' => __('sales.payment.mixed'),
            default => $method ?? '',
        };
    }

    public static function expenseType(?string $type): string
    {
        return match ($type) {
            'Tetap' => __('operational.type.fixed'),
            'Variabel' => __('operational.type.variable'),
            default => $type ?? '',
        };
    }

    public static function rawMaterialCategory(?string $key): string
    {
        return match ($key) {
            'Kering' => __('stock.category.dry'),
            'Basah' => __('stock.category.wet'),
            'Padat' => __('stock.category.solid'),
            default => __('stock.category.solid'),
        };
    }

    public static function rawMaterialCategoryKey(string $value): string
    {
        return match ($value) {
            'kering' => __('stock.category.dry'),
            'basah' => __('stock.category.wet'),
            'padat' => __('stock.category.solid'),
            default => __('stock.category.solid'),
        };
    }
}
