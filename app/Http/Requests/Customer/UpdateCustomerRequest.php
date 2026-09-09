<?php

namespace App\Http\Requests\Customer;

class UpdateCustomerRequest extends StoreCustomerRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('customers.edit');
    }

    public function rules(): array
    {
        // Same field rules as creation; consumer_id/barcode are immutable and never accepted here.
        return parent::rules();
    }
}
