<?php

namespace App\Http\Requests\Holiday;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('holidays.edit');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'date' => ['required', 'date'],
            'type' => ['required', 'in:national,regional,company,optional'],
            'is_recurring_yearly' => ['nullable', 'boolean'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
