<?php

namespace App\Http\Requests\Bill;

use Illuminate\Foundation\Http\FormRequest;

class GenerateBillsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('bills.generate');
    }

    public function rules(): array
    {
        return [
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2000,2100'],
            'customer_ids' => ['nullable', 'array'],
            'customer_ids.*' => ['integer', 'exists:customers,id'],
            'gst_percent' => ['nullable', 'numeric', 'min:0', 'max:28'],
            'delivery_charges' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
