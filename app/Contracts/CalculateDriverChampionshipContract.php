<?php

namespace App\Contracts;

use App\Models\Season;

interface CalculateDriverChampionshipContract
{
    public function __construct(Season $season);

    public function hasResults(): bool;

    public function clearExistingStandings(): void;

    public function cacheResults(): void;

    public function sortResults(): void;

    public function addPositionToResults(): void;

    public function storeStandings(): void;
}
