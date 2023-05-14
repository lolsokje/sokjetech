<?php

namespace App\Contracts;

interface HasSessions
{
    public function sessions(): int;
}
