<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalCost extends Model
{
    use LogsActivity;

    protected static string $activityMenu = 'biaya operasional';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'expense_category_id',
        'tanggal',
        'kat',
        'desk',
        'jumlah',
        'jenis',
        'journal_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function journalTransaction(): BelongsTo
    {
        return $this->belongsTo(JournalTransaction::class, 'journal_transaction_id');
    }

    public function activityObjectName(): string
    {
        return $this->kat ?: $this->id;
    }
}
