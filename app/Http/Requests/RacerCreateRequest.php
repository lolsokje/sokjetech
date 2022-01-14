<?php

namespace App\Http\Requests;

use App\Rules\UniqueActiveDriversInSeason;
use App\Rules\UniqueDriversInRequest;
use App\Rules\UniqueNumbersInRequest;
use App\Rules\UniqueNumbersInSeason;
use Illuminate\Foundation\Http\FormRequest;

class RacerCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $parameters = $this->route()->parameters;

        return [
            'drivers' => ['required', 'array', new UniqueNumbersInRequest, new UniqueDriversInRequest],
            'drivers.*.driver_id' => ['required', 'exists:drivers,id', new UniqueActiveDriversInSeason($parameters)],
            'drivers.*.number' => ['required', 'integer', new UniqueNumbersInSeason($parameters)],
        ];
    }
}
