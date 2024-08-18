<?php

declare(strict_types=1);

namespace App\Http\Requests\Season\Development;

use App\ValueObjects\Season\Development\DevelopmentEntity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

final class StoreDevelopmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'entities' => ['required', 'array'],
            'entities.*.id' => ['required'],
            'entities.*.label' => ['required'],
            'entities.*.current' => ['required', 'int'],
            'entities.*.styleString' => ['nullable'],
            'entities.*.extra' => ['nullable'],
            'entities.*.min' => ['nullable'],
            'entities.*.max' => ['nullable'],
            'entities.*.rng' => ['required', 'numeric'],
            'entities.*.new' => ['numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'entities.*.new' => 'The new rating must be at least 0',
        ];
    }

    /**
     * @return Collection<DevelopmentEntity>
     */
    public function entities(): Collection
    {
        return DevelopmentEntity::fromRequest($this->validated('entities'));
    }
}
