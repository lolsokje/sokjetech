<?php

namespace App\Contracts;

interface RaceDuration
{
    public function editable(): string|int|array;

    public function readable(): string;

    public function postfix(): string;
}
