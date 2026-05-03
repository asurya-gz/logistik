<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ItemPrice::class);
    }

    public function logistics(): HasMany
    {
        return $this->hasMany(Logistics::class);
    }

    public function activePrices(): HasMany
    {
        return $this->prices()->orderByDesc('effective_date')->orderByDesc('id');
    }
}
