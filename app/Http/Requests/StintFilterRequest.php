<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StintFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'min_rng' => ['nullable', 'integer'],
            'max_rng' => ['nullable', 'integer'],
            'reliability' => ['nullable', 'in:true,false'],
            'use_driver_rating' => ['nullable', 'in:true,false'],
            'use_team_rating' => ['nullable', 'in:true,false'],
            'use_engine_rating' => ['nullable', 'in:true,false'],
        ];
    }

    public function min(): ?int
    {
        return $this->validated('min_rng');
    }

    public function max(): ?int
    {
        return $this->validated('max_rng');
    }

    public function reliability(): ?bool
    {
        return $this->convertBoolean($this->validated('reliability'));
    }

    public function useTeamRating(): ?bool
    {
        return $this->convertBoolean($this->validated('use_team_rating'));
    }

    public function useDriverRating(): ?bool
    {
        return $this->convertBoolean($this->validated('use_driver_rating'));
    }

    public function useEngineRating(): ?bool
    {
        return $this->convertBoolean($this->validated('use_engine_rating'));
    }

    private function convertBoolean(?string $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        return $value === 'true';
    }
}
