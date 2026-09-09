<?php

namespace App\Http\Requests\CashSale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCashSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('cash-sales.create');
    }

    public function rules(): array
    {
        return [
            'sale_date' => ['required', 'date'],
            'shift' => ['required', Rule::in(['morning', 'evening'])],
            'milk_type' => ['required', Rule::in(['cow', 'buffalo', 'mixed'])],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method_id' => ['nullable', 'exists:payment_methods,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
