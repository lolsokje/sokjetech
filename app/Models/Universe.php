<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Universe extends Model
{
    use HasFactory, Uuids;

    public const VISIBILITY_PUBLIC = 1;
    public const VISIBILITY_PRIVATE = 2;
    public const VISIBILITY_AUTH = 3;

    protected $guarded = [];

    public static function visibilities(): array
    {
        return array_keys(self::visibilityLabels());
    }

    public static function visibilityLabels(): array
    {
        return [
            self::VISIBILITY_PUBLIC => 'public',
            self::VISIBILITY_PRIVATE => 'private',
            self::VISIBILITY_AUTH => 'auth',
        ];
    }

    public function isPublic(): bool
    {
        return $this->visibility === self::VISIBILITY_PUBLIC;
    }

    public function isPrivate(): bool
    {
        return $this->visibility === self::VISIBILITY_PRIVATE;
    }

    public function isAuthRequired(): bool
    {
        return $this->visibility === self::VISIBILITY_AUTH;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
