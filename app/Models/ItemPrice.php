<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPrice extends Model
{
    protected $fillable = [
        'item_id',
        'branch_id',
        'price',
        'effective_date',
        'created_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'price' => 'decimal:2',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeEffectiveOn(Builder $query, string $date): Builder
    {
        return $query->whereDate('effective_date', '<=', $date);
    }
}
