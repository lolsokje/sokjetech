<?php

namespace App\Traits;

trait Snowflake
{
    public function getSnowflakeCasts(): array
    {
        $casts = ['id' => 'string'];

        foreach (array_keys($this->attributes) as $attribute) {
            if (str_contains($attribute, '_id')) {
                $casts[$attribute] = 'string';
            }
        }

        return $casts;
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getCasts(): array
    {
        $class = static::class;

        if (method_exists($class, 'getSnowflakeCasts')) {
            $this->casts = array_merge($this->casts, $this->getSnowflakeCasts());
        }

        return parent::getCasts();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $key = resolve('snowflake')->id();
                $model->{$model->getKeyName()} = $key;
            }
        });
    }
}
