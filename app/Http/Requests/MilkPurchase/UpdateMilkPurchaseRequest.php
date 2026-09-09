<?php

namespace App\Http\Requests\MilkPurchase;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMilkPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('milk-purchases.edit');
    }

    /**
     * Quantity/rate/date/shift/supplier are intentionally NOT editable here:
     * changing them after the fact would silently desync the stock ledger
     * (and any sales already made against that stock). Use a wastage/
     * adjustment entry to correct quantity mistakes instead.
     */
    public function rules(): array
    {
        return [
            'fat_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'snf_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'quality_grade' => ['nullable', 'string', 'max:20'],
            'payment_status' => ['required', Rule::in(['paid', 'partial', 'pending'])],
            'payment_method_id' => ['nullable', 'exists:payment_methods,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
