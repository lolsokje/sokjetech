<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Season extends Model
{
    use HasFactory, Uuids;

    protected $casts = [
        'year' => 'integer',
    ];

    protected $appends = [
        'fullName',
    ];

    public function getFullNameAttribute(): string
    {
        return "$this->year {$this->series->name} season";
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}
