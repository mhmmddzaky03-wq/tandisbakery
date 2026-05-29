<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'object',
        'menu',
    ];

    protected $appends = [
        'formatted_log',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedLogAttribute(): string
    {
        $at = $this->created_at
            ->timezone('Asia/Jakarta')
            ->format('d/m/Y H:i:s');

        return sprintf(
            '%s %s melakukan %s %s %s pada %s WIB',
            strtolower($this->user_role ?? 'guest'),
            strtolower($this->user_name ?? 'guest'),
            $this->action,
            $this->menu,
            $this->object,
            $at
        );
    }

    public static function record(string $action, string $object, string $menu): void
    {
        $user = Auth::user();

        static::create([
            'user_id'   => $user?->id,
            'user_name' => $user?->name ?? 'Guest',
            'user_role' => $user?->role ?? 'guest',
            'action'    => $action,
            'object'    => $object,
            'menu'      => $menu,
        ]);
    }
}
