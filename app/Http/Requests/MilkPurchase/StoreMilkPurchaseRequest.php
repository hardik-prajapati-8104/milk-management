<?php

namespace App\Http\Requests\MilkPurchase;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMilkPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('milk-purchases.create');
    }

    public function rules(): array
    {
        return [
            'purchase_date' => ['required', 'date'],
            'purchase_time' => ['nullable', 'date_format:H:i'],
            'shift' => ['required', Rule::in(['morning', 'evening'])],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'milk_type' => ['required', Rule::in(['cow', 'buffalo', 'mixed'])],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'rate' => ['required', 'numeric', 'min:0'],
            'fat_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'snf_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'quality_grade' => ['nullable', 'string', 'max:20'],
            'payment_status' => ['required', Rule::in(['paid', 'partial', 'pending'])],
            'payment_method_id' => ['nullable', 'exists:payment_methods,id'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
