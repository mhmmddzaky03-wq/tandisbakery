<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Support\FormatHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatchBahanDasar extends Model
{
    use LogsActivity;

    protected static string $activityMenu = 'batch bahan dasar';

    protected $table = 'batch_bahan_dasar';

    protected $fillable = [
        'bahan_dasar_id',
        'tanggal',
        'jumlah',
        'sisa',
        'total_biaya',
        'catatan',
        'journal_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'float',
            'sisa' => 'float',
            'total_biaya' => 'integer',
        ];
    }

    public function bahanDasar(): BelongsTo
    {
        return $this->belongsTo(BahanDasar::class, 'bahan_dasar_id');
    }

    public function pemakaianBahanBaku(): HasMany
    {
        return $this->hasMany(PemakaianBahanBakuAdonan::class, 'batch_bahan_dasar_id');
    }

    public function unitPricePerBase(): int
    {
        $jumlah = (float) $this->jumlah;

        if ($jumlah <= 0) {
            return 0;
        }

        return (int) round((int) $this->total_biaya / $jumlah);
    }

    public function remainingValue(): int
    {
        $jumlah = (float) $this->jumlah;
        $sisa = (float) $this->sisa;

        if ($jumlah <= 0 || $sisa <= 0) {
            return 0;
        }

        return (int) round((int) $this->total_biaya * ($sisa / $jumlah));
    }

    public function displayLabel(?string $satuan = null): string
    {
        $unit = $satuan ?? $this->bahanDasar?->satuan ?? 'g';

        return 'Batch '.FormatHelper::dateId($this->tanggal?->format('Y-m-d'))
            .' · '.FormatHelper::formatQtyOne((float) $this->sisa).' '.$unit;
    }

    public function activityObjectName(): string
    {
        return $this->bahanDasar?->nama ?? $this->bahan_dasar_id;
    }
}
