<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsPhoto extends Model
{
    protected $fillable = [
        'logistics_id',
        'photo_path',
        'sort_order',
    ];

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(Logistics::class);
    }
}
