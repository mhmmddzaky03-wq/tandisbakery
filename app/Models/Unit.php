<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use LogsActivity;

    /** @var list<string> */
    public const PROTECTED_NAMES = ['kg', 'L'];

    protected static string $activityMenu = 'satuan';

    protected $fillable = [
        'nama',
    ];

    public static function ensureProtectedExist(): void
    {
        foreach (self::PROTECTED_NAMES as $nama) {
            self::firstOrCreate(['nama' => $nama]);
        }
    }

    /** @return \Illuminate\Support\Collection<int, self> */
    public static function orderedForDisplay()
    {
        self::ensureProtectedExist();

        return self::query()
            ->orderBy('nama')
            ->get()
            ->sortBy(fn (self $unit): array => [
                $unit->isProtected() ? 0 : 1,
                $unit->nama,
            ])
            ->values();
    }

    public function isProtected(): bool
    {
        return in_array($this->nama, self::PROTECTED_NAMES, true);
    }

    public function isInUse(): bool
    {
        return RawMaterial::where('satuan', $this->nama)->exists();
    }

    public function canBeDeleted(): bool
    {
        return ! $this->isProtected() && ! $this->isInUse();
    }

    public function canBeRenamed(): bool
    {
        return ! $this->isProtected() && ! $this->isInUse();
    }
}
