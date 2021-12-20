<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Circuit extends Model
{
    use HasFactory, Uuids;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
