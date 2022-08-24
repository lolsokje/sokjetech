<?php

namespace App\Models;

use App\Builders\BugBuilder;
use App\Enums\BugStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bug extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => BugStatus::class,
    ];

    protected $appends = [
        'status_text',
    ];

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function type(): Attribute
    {
        return Attribute::make(
            get: fn (string $type) => ucfirst($type),
            set: fn (string $type) => strtolower($type),
        );
    }

    public function statusText(): Attribute
    {
        return Attribute::get(fn () => ucfirst($this->attributes['status']));
    }

    public static function query(): BugBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): BugBuilder
    {
        return new BugBuilder($query);
    }
}
