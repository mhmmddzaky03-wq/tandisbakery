<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $primaryKey = 'kode';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'kode',
        'nama',
        'posisi',
        'grup',
    ];

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'account_kode', 'kode');
    }
}
