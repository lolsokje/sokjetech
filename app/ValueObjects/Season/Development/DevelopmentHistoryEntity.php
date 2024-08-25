<?php

declare(strict_types=1);

namespace App\ValueObjects\Season\Development;

use App\Models\EngineSeason;
use App\Models\Entrant;
use App\Models\Racer;

final readonly class DevelopmentHistoryEntity
{
    /**
     * @param array<int> $history
     */
    private function __construct(
        public string $label,
        public array $history,
        public string $primary,
        public string $secondary,
        public string $accent,
        public bool $dash = false,
    ) {
    }

    public static function fromDriver(
        Racer $racer,
        array $history,
        bool $dash,
    ): self {
        return new self(
            label: $racer->driver->full_name,
            history: $history,
            primary: $racer->entrant->primary_colour,
            secondary: $racer->entrant->secondary_colour,
            accent: $racer->entrant->accent_colour,
            dash: $dash,
        );
    }

    public static function fromTeam(
        Entrant $team,
        array $history,
    ): self {
        return new self(
            label: $team->full_name,
            history: $history,
            primary: $team->primary_colour,
            secondary: $team->secondary_colour,
            accent: $team->accent_colour,
            dash: false,
        );
    }

    public static function fromEngine(
        EngineSeason $engine,
        array $history,
    ): self {
        return new self(
            label: $engine->name,
            history: $history,
            primary: '',
            secondary: '',
            accent: '',
            dash: false,
        );
    }
}
