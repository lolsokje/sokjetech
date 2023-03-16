<?php

namespace App\DataTransferObjects\Configuration;

use App\Http\Requests\PointSystemConfigurationRequest;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class PointSystemConfiguration implements Arrayable
{
    public function __construct(
        public readonly ?bool $fastestLapPointAwarded = false,
        public readonly ?bool $polePositionPointAwarded = false,
        public readonly ?int $fastestLapPointAmount = 0,
        public readonly ?int $polePositionPointAmount = 0,
        public readonly ?string $fastestLapDetermination = 'best_last_stint',
        public readonly ?int $fastestLapMinRng = 0,
        public readonly ?int $fastestLapMaxRng = 0,
        public readonly ?Collection $points = new Collection,
    ) {
    }

    public static function fromRequest(PointSystemConfigurationRequest $request): PointSystemConfiguration
    {
        $validated = $request->validated();

        return new self(
            $validated['fastest_lap_point_awarded'] ?? null,
            $validated['pole_position_point_awarded'] ?? null,
            $validated['fastest_lap_point_amount'] ?? null,
            $validated['pole_position_point_amount'] ?? null,
            $validated['fastest_lap_determination'] ?? null,
            $validated['fastest_lap_min_rng'] ?? null,
            $validated['fastest_lap_max_rng'] ?? null,
            $request->points(),
        );
    }

    public function toArray(): array
    {
        return [
            'fastest_lap_point_awarded' => $this->fastestLapPointAwarded,
            'pole_position_point_awarded' => $this->polePositionPointAwarded,
            'fastest_lap_point_amount' => $this->fastestLapPointAmount,
            'pole_position_point_amount' => $this->polePositionPointAmount,
            'fastest_lap_determination' => $this->fastestLapDetermination,
            'fastest_lap_min_rng' => $this->fastestLapMinRng,
            'fastest_lap_max_rng' => $this->fastestLapMaxRng,
        ];
    }
}
