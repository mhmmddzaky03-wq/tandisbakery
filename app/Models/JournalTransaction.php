<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalTransaction extends Model
{
    protected $fillable = [
        'tanggal',
        'deskripsi',
        'ref',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'journal_transaction_id');
    }
}
