<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    protected $fillable = [
        'journal_transaction_id',
        'account_kode',
        'debit',
        'credit',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(JournalTransaction::class, 'journal_transaction_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_kode', 'kode');
    }
}
