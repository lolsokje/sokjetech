<?php

namespace App\Http\Requests;

use App\Rules\YearUniqueInSeries;
use Illuminate\Foundation\Http\FormRequest;

class SeasonCreateRequest extends FormRequest
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
        return [
            'year' => ['required', 'integer', new YearUniqueInSeries($this->route()->parameters())],
        ];
    }
}
