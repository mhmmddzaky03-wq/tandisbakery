<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalCost extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tanggal',
        'kat',
        'desk',
        'jumlah',
        'jenis',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }
}
