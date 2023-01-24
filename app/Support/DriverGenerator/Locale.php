<?php

namespace App\Support\DriverGenerator;

class Locale
{
    public function __construct(readonly private string $language, readonly private string $country)
    {
    }

    public function country(): string
    {
        return substr($this->country, -2);
    }

    public function language(): string
    {
        return $this->language;
    }

    public function locale(): string
    {
        return "{$this->language()}_{$this->country()}";
    }
}
