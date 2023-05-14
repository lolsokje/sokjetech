<?php

namespace App\DataTransferObjects;

readonly class QualifyingDriver
{
    public int $id;

    public int $entrantId;

    public QualifyingDriverResult $result;

    public QualifyingDriverRating $rating;

    public function __construct(array $driver)
    {
        $this->id = $driver['id'];
        $this->entrantId = $driver['entrant_id'];
        $this->result = new QualifyingDriverResult($driver);
        $this->rating = new QualifyingDriverRating($driver);
    }
}
