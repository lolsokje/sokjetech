<?php

namespace App\Traits;

trait Snowflake
{
    public function getIncrementing(): bool
    {
        return false;
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
