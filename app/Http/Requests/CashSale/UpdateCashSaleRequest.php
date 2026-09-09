<?php

namespace App\Http\Requests\CashSale;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('cash-sales.edit');
    }

    public function rules(): array
    {
        return [
            'payment_method_id' => ['nullable', 'exists:payment_methods,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
