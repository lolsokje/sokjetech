<?php

namespace App\DataTransferObjects\RaceWeekend;

class QualifyingDriver extends RaceWeekendDriver
{
    public QualifyingDriverResult $result;

    private function __construct(array $driver)
    {
        parent::__construct($driver);

        $this->result = new QualifyingDriverResult($driver);
    }

    public static function fromRequest(array $driver): QualifyingDriver
    {
        return new self($driver);
    }
}
