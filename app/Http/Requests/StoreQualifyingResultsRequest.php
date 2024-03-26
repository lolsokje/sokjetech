<?php

namespace App\Http\Requests;

use App\DataTransferObjects\Race\QualifyingDetails;
use App\DataTransferObjects\RaceWeekend\QualifyingDriver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class StoreQualifyingResultsRequest extends FormRequest
{
    public function details(): QualifyingDetails
    {
        return new QualifyingDetails(
            session: $this->validated('details.current_session'),
            run: $this->validated('details.current_run'),
        );
    }

    public function drivers(): Collection
    {
        return collect($this->validated('drivers'))->map(fn (array $driver) => QualifyingDriver::fromRequest($driver));
    }

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'drivers' => ['required', 'array'],
            'details' => ['required', 'array'],
            'details.current_session' => ['required', 'int', 'min:0'],
            'details.current_run' => ['required', 'int', 'min:0'],
        ];
    }
}
