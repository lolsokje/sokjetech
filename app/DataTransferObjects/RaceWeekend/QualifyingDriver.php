<?php

namespace App\DataTransferObjects\RaceWeekend;

class QualifyingDriver extends RaceWeekendDriver
{
    public QualifyingDriverResult $result;

    public function __construct(array $driver)
    {
        parent::__construct($driver);

        $this->result = new QualifyingDriverResult($driver);
    }
}
