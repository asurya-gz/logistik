<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsPhotoItem extends Model
{
    protected $fillable = [
        'logistics_photo_id',
        'item_id',
        'quantity',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(LogisticsPhoto::class, 'logistics_photo_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
