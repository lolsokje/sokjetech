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
        return $this->parseDate($this->get('start'));
    }

    public function end(): ?DateTimeImmutable
    {
        return $this->parseDate($this->get('end'));
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

    private function parseDate(?string $date): ?DateTimeImmutable
    {
        return $date ? DateTimeImmutable::createFromFormat('Y-m-d', $date) : null;
    }
}
