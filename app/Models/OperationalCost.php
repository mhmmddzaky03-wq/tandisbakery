<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class OperationalCost extends Model
{
    use LogsActivity;

    protected static string $activityMenu = 'biaya operasional';
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
