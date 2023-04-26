<?php

namespace App\DataTransferObjects;

use JsonSerializable;

class StatData implements JsonSerializable
{
    public function __construct(public readonly string $label, public readonly string $value)
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'label' => $this->label,
            'value' => $this->value,
        ];
    }
}
