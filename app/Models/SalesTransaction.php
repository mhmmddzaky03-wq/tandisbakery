<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesTransaction extends Model
{
    use LogsActivity;

    protected static string $activityMenu = 'transaksi penjualan';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tanggal',
        'total',
        'metode',
        'jumlah',
        'journal_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function journalTransaction(): BelongsTo
    {
        return $this->belongsTo(JournalTransaction::class);
    }
}
