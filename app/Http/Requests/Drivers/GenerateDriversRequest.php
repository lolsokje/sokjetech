<?php

namespace App\Http\Requests\Drivers;

use DateTimeImmutable;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class GenerateDriversRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::check('owns-universe', $this->route('universe'));
    }

    public function rules(): array
    {
        return [
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
            'amount' => ['required', 'min:1'],
            'language' => ['present', 'nullable', 'string'],
            'gender' => ['present', 'nullable', 'in:null,male,female'],
        ];
    }

    public function start(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('Y-m-d', $this->get('start'));
    }

    public function end(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('Y-m-d', $this->get('end'));
    }

    public function amount(): int
    {
        return (int) $this->get('amount');
    }

    public function language(): ?string
    {
        return $this->get('language') === 'null' ? null : $this->get('language');
    }

    public function gender(): ?string
    {
        return $this->get('gender') === 'null' ? null : $this->get('gender');
    }
}
