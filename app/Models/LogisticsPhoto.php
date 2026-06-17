<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LogisticsPhoto extends Model
{
    public const STATUS_NONE = 'none';
    public const STATUS_OK = 'ok';
    public const STATUS_REJECT = 'reject';

    public const STATUSES = [
        self::STATUS_NONE => 'Belum dinilai',
        self::STATUS_OK => 'OK',
        self::STATUS_REJECT => 'Reject',
    ];

    protected $fillable = [
        'logistics_id',
        'photo_path',
        'sort_order',
        'tanggal',
        'status',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(Logistics::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LogisticsPhotoItem::class, 'logistics_photo_id');
    }
}
