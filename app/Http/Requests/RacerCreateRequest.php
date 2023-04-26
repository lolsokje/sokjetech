<?php

namespace App\Http\Requests;

use App\Rules\DriverNotRetiredRule;
use App\Rules\UniqueActiveDriversInSeason;
use App\Rules\UniqueDriversInRequest;
use App\Rules\UniqueNumbersInRequest;
use App\Rules\UniqueNumbersInSeason;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class RacerCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::check('owns-universe', $this->route('season')->universe);
    }

    public function rules(): array
    {
        $parameters = $this->route()->parameters;

        return [
            'drivers' => ['required', 'array', new UniqueNumbersInRequest, new UniqueDriversInRequest],
            'drivers.*.driver_id' => [
                'required',
                'exists:drivers,id',
                new UniqueActiveDriversInSeason($parameters),
                new DriverNotRetiredRule,
            ],
            'drivers.*.number' => ['required', 'integer', new UniqueNumbersInSeason($parameters), 'min:0', 'max:9999'],
        ];
    }

    public function messages(): array
    {
        return [
            'drivers.*.number.min' => 'Driver numbers must not be less than 0',
            'drivers.*.number.max' => 'Driver numbers must not be greater than 9999',
        ];
    }

    public function drivers(): array
    {
        return $this->validated('drivers');
    }
}
