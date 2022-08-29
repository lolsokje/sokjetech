<?php

namespace App\Http\Requests;

use App\DataTransferObjects\PointsData;
use App\Enums\FastestLapDetermination;
use App\Rules\ValidPointsRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Enum;

class PointSystemConfigurationRequest extends FormRequest
{
    /**
     * Override default method to remove the 'points' array, as it's not required
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);
        unset($validated['points']);
        return $validated;
    }

    public function points(): Collection
    {
        $points = collect($this->validated('points'));

        return $points->map(fn (array $pointDistribution) => new PointsData($pointDistribution));
    }

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'points' => ['required', 'array', new ValidPointsRule()],
            'fastest_lap_point_awarded' => ['boolean', 'nullable'],
            'pole_position_point_awarded' => ['boolean', 'nullable'],
            'fastest_lap_point_amount' => ['integer', 'min:0', 'nullable'],
            'pole_position_point_amount' => ['integer', 'min:0', 'nullable'],
            'fastest_lap_determination' => [
                'string',
                'nullable',
                'required_if:fastest_lap_point_awarded,true',
                new Enum(FastestLapDetermination::class),
            ],
            'fastest_lap_min_rng' => $this->getFastestLapRngRules(),
            'fastest_lap_max_rng' => $this->getFastestLapRngRules(),
        ];
    }

    private function getFastestLapRngRules(): array
    {
        return [
            'integer',
            'min:0',
            'nullable',
            'required_if:fastest_lap_determination,separate_stint',
        ];
    }
}
