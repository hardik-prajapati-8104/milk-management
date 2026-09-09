<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('inventory.create');
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'movement_date' => ['required', 'date'],
            'type' => ['required', Rule::in(['collection', 'purchase', 'sale', 'wastage', 'transfer_in', 'transfer_out', 'adjustment'])],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'rate' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ];
    }
}
