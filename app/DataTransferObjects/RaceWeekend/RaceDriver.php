<?php

namespace App\DataTransferObjects\RaceWeekend;

class RaceDriver extends RaceWeekendDriver
{
    public RaceDriverResult $result;

    public function __construct(array $driver)
    {
        parent::__construct($driver);

        $this->result = new RaceDriverResult($driver);
    }
}
