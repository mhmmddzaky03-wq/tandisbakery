<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use LogsActivity;

    protected static string $activityMenu = 'satuan';
    protected $fillable = [
        'nama',
    ];
}
