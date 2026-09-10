<?php

namespace App\Http\Requests\Birthday;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBirthdayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('birthdays.edit');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'category' => ['required', 'in:employee,customer,supplier,other'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
            'reminder_days_before' => ['nullable', 'integer', 'min:0', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
