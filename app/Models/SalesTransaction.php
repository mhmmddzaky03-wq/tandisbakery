<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTransaction extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tanggal',
        'total',
        'metode',
        'jumlah',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }
}
