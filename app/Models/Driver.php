<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Driver extends Model
{
    use HasFactory, Uuids;

    protected $guarded = [];

    protected $appends = [
        'fullName',
        'readableDob',
        'editDob',
    ];

    protected $casts = [
        'dob' => 'datetime',
    ];

    public function getReadableDobAttribute(): string
    {
        return $this->dob->format('F jS, Y');
    }

    public function getEditDobAttribute(): string
    {
        return $this->dob->format('Y-m-d');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} $this->last_name");
    }

    public function universe(): BelongsTo
    {
        return $this->belongsTo(Universe::class);
    }

    public function user(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Universe::class);
    }
}
