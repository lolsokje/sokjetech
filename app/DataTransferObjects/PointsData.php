<?php

namespace App\DataTransferObjects;

class PointsData
{
    protected int $position;
    protected int $points;

    public function __construct(array $points)
    {
        $this->position = $points['position'];
        $this->points = $points['points'];
    }

    public function position(): int
    {
        return $this->position;
    }

    public function points(): int
    {
        return $this->points;
    }
}