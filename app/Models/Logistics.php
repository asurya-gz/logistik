<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Logistics extends Model
{
    protected $fillable = [
        'item_id',
        'nama_barang',
        'kategori',
        'jumlah',
        'unit_price_snapshot',
        'total_price',
        'tanggal',
        'keterangan',
        'office_note',
        'photo_path',
        'status',
        'branch_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'unit_price_snapshot' => 'decimal:2',
            'total_price' => 'decimal:2',
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

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(LogisticsPhoto::class)->orderBy('sort_order');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isFullAccess()
            ? $query
            : $query->where('branch_id', $user->branch_id);
    }
}
