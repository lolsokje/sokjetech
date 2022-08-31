<?php

namespace App\Http\Requests;

use App\Rules\YearUniqueInSeries;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class SeasonCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::check('owns-universe', $this->route('series')->universe);
    }

    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', new YearUniqueInSeries($this->route()->parameters())],
            'name' => ['required', 'max:255'],
        ];
    }

    public function data(): array
    {
        $name = "{$this->route('series')->name} season";
        return array_merge(
            $this->validated(),
            ['name' => $name],
        );
    }
}
