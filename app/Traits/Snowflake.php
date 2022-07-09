<?php

namespace App\Traits;

trait Snowflake
{
    public function getSnowflakeCasts(): array
    {
        return [
            'id' => 'string',
        ];
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getCasts(): array
    {
        $class = static::class;

        foreach (class_uses_recursive($class) as $trait) {
            $method = 'get' . class_basename($trait) . 'Casts';

            if (method_exists($class, $method)) {
                $this->casts = array_merge($this->casts, $this->{$method}());
            }
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
