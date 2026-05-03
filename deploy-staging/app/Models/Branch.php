<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function logistics(): HasMany
    {
        return $this->hasMany(Logistics::class);
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(Upload::class);
    }
}
