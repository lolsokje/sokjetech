<?php

namespace App\Http\Requests;

use App\Enums\ReliabilityReasonTypes;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\In;

class StoreReliabilityConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::check('owns-universe', $this->route('season')->universe);
    }

    public function prepareForValidation()
    {
        $reasons = $this->get('reasons') ?? [];

        foreach ($reasons as $key => $reason) {
            $reasons[$key] = explode(PHP_EOL, $reason);
        }

        $this->merge([
            'reasons' => $reasons,
            'reason_keys' => array_keys($reasons),
        ]);
    }

    public function rules(): array
    {
        return [
            'min_rng' => ['required', 'integer', 'min:0'],
            'max_rng' => ['required', 'integer', 'min:0'],
            'reasons' => ['array', 'required'],
            'reasons.*' => ['array'],
            'reason_keys' => ['array', new In(ReliabilityReasonTypes::keyNames())],
        ];
    }
}
