<?php

namespace App\Http\Requests;

use App\Rules\ValidLaptimeRule;
use Illuminate\Foundation\Http\FormRequest;

class CircuitVariationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'length' => ['required', 'numeric', 'min:1'],
            'base_laptime' => ['required', new ValidLaptimeRule],
        ];
    }
}
