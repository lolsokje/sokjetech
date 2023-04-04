<?php

namespace App\Http\Requests\Drivers;

use DateTimeImmutable;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use LilPecky\RandomPersonGenerator\Amount;
use LilPecky\RandomPersonGenerator\Support\Gender;

class GenerateDriversRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::check('owns-universe', $this->route('universe'));
    }

    public function rules(): array
    {
        return [
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
            'amount' => ['required', 'min:1'],
            'language' => ['present', 'nullable', 'string'],
            'gender' => ['present', 'nullable', 'in:null,male,female'],
        ];
    }

    public function start(): ?DateTimeImmutable
    {
        $start = $this->get('start');

        return $start ? DateTimeImmutable::createFromFormat('Y-m-d', $this->get('start')) : null;
    }

    public function end(): ?DateTimeImmutable
    {
        $end = $this->get('end');

        return $end ? DateTimeImmutable::createFromFormat('Y-m-d', $this->get('end')) : null;
    }

    public function amount(): Amount
    {
        return new Amount((int) $this->get('amount'));
    }

    public function language(): ?string
    {
        return $this->get('language') === 'null' ? null : $this->get('language');
    }

    public function gender(): ?Gender
    {
        return $this->get('gender') === 'null' ? null : Gender::tryFrom($this->get('gender'));
    }
}
