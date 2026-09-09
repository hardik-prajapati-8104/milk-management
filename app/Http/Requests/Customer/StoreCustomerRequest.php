<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('customers.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'mobile' => ['required', 'string', 'max:15', 'regex:/^[0-9+\-\s]+$/'],
            'alternative_mobile' => ['nullable', 'string', 'max:15'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string'],
            'village_id' => ['nullable', 'exists:villages,id'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'route_id' => ['nullable', 'exists:routes,id'],

            'milk_type' => ['required', Rule::in(['cow', 'buffalo', 'mixed'])],
            'morning_rate' => ['required', 'numeric', 'min:0'],
            'evening_rate' => ['required', 'numeric', 'min:0'],
            'default_qty_morning' => ['nullable', 'numeric', 'min:0'],
            'default_qty_evening' => ['nullable', 'numeric', 'min:0'],

            'customer_category_id' => ['nullable', 'exists:customer_categories,id'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'notes' => ['nullable', 'string'],
            'joining_date' => ['nullable', 'date'],

            'identity_proof_type' => ['nullable', 'string', 'max:50'],
            'identity_proof_number' => ['nullable', 'string', 'max:50'],

            'opening_balance' => ['nullable', 'numeric'],

            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.regex' => 'Please enter a valid mobile number.',
        ];
    }
}
