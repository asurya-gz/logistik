<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogisticsSupportingPhoto extends Model
{
    protected $table = 'logistics_supporting_photos';

    protected $fillable = [
        'logistics_id',
        'uploaded_by',
        'catatan',
        'photo_path',
    ];

    public function logistics(): BelongsTo
    {
        return $this->belongsTo(Logistics::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
