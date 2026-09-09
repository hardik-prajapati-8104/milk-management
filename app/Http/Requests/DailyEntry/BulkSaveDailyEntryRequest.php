<?php

namespace App\Http\Requests\DailyEntry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkSaveDailyEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('daily-entries.create') || $this->user()->can('daily-entries.edit');
    }

    public function rules(): array
    {
        return [
            'entry_date' => ['required', 'date'],
            'shift' => ['required', Rule::in(['morning', 'evening'])],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.customer_id' => ['required', 'integer', 'exists:customers,id'],
            'entries.*.quantity' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'entries.*.is_absent' => ['nullable', 'boolean'],
            'entries.*.is_holiday' => ['nullable', 'boolean'],
            'entries.*.remarks' => ['nullable', 'string', 'max:255'],
        ];
    }
}
