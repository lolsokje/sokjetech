<?php

namespace App\Models;

use App\Builders\SuggestionBuilder;
use App\Enums\SuggestionStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suggestion extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => SuggestionStatus::class,
    ];

    protected $appends = [
        'status_text',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusText(): Attribute
    {
        return Attribute::get(fn () => ucfirst($this->attributes['status']));
    }

    public static function query(): SuggestionBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): SuggestionBuilder
    {
        return new SuggestionBuilder($query);
    }
}
