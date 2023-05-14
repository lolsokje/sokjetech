<?php

namespace App\DataTransferObjects\RaceWeekend;

readonly class RaceDriver extends RaceWeekendDriver
{
    public RaceDriverResult $result;

    public function __construct(array $driver)
    {
        parent::__construct($driver);

        $this->result = new RaceDriverResult($driver);
    }
}
