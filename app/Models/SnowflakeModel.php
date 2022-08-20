<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnowflakeModel extends Model
{
    public function getIncrementing(): bool
    {
        return false;
    }

    public function getCasts(): array
    {
        return array_merge($this->casts, $this->getSnowflakeCasts());
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Model $model) {
            if (!$model->getKey()) {
                $key = resolve('snowflake')->id();
                $model->{$model->getKeyName()} = $key;
            }
        });
    }

    private function getSnowflakeCasts(): array
    {
        $casts = ['id' => 'string'];

        foreach (array_keys($this->attributes) as $attribute) {
            if (str_contains($attribute, '_id')) {
                $casts[$attribute] = 'string';
            }
        }

        return $casts;
    }
}
