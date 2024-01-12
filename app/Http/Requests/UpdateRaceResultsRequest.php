<?php

namespace App\Http\Requests;

use App\DataTransferObjects\Race\Result\RaceResult;
use App\DataTransferObjects\Race\Result\RaceResultCollection;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRaceResultsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'current_lap' => ['required', 'int', 'min:1'],
            'results.*' => ['required', 'array'],
            'results.*.id' => ['required', 'exists:race_results,id'],
            'results.*.position' => ['required', 'int', 'min:1'],
            'results.*.stints' => ['required', 'array'],
            'results.*.stints.*' => ['int', 'min:0'],
            'results.*.total' => ['required', 'min:1'],
        ];
    }

    public function results(): RaceResultCollection
    {
        $results = [];

        foreach ($this->validated('results') as $result) {
            $results[] = new RaceResult(
                id: $result['id'],
                position: $result['position'],
                total: $result['total'],
                stints: $result['stints'],
            );
        }

        return new RaceResultCollection($results);
    }
}
