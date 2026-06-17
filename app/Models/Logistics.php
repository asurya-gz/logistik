<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
        'logistik_note',
        'logistik_noted_by',
        'logistik_noted_at',
        'finalized_at',
        'finalized_by',
        'photo_path',
        'status',
        'branch_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'logistik_noted_at' => 'datetime',
            'finalized_at' => 'datetime',
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

    public function supportingPhotos(): HasMany
    {
        return $this->hasMany(LogisticsSupportingPhoto::class)->latest();
    }

    public function logistikNotedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logistik_noted_by');
    }

    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function isFinalized(): bool
    {
        return $this->finalized_at !== null;
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isFullAccess()
            ? $query
            : $query->where('branch_id', $user->branch_id);
    }
}
