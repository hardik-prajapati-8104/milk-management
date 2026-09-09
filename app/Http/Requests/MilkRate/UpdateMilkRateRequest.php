<?php

namespace App\Http\Requests\MilkRate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMilkRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('milk-rates.edit');
    }

    public function rules(): array
    {
        return [
            'effective_date' => [
                'required', 'date',
                Rule::unique('milk_rates', 'effective_date')->ignore($this->route('milk_rate')),
            ],
            'cow_morning_rate' => ['required', 'numeric', 'min:0'],
            'cow_evening_rate' => ['required', 'numeric', 'min:0'],
            'buffalo_morning_rate' => ['required', 'numeric', 'min:0'],
            'buffalo_evening_rate' => ['required', 'numeric', 'min:0'],
            'mixed_rate' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'effective_date.unique' => 'A rate card already exists for this effective date.',
        ];
    }
}
