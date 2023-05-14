<?php

namespace App\DataTransferObjects\RaceWeekend;

readonly class QualifyingDriver extends RaceWeekendDriver
{
    public QualifyingDriverResult $result;

    public function __construct(array $driver)
    {
        parent::__construct($driver);

        $this->result = new QualifyingDriverResult($driver);
    }
}
