<?php

namespace App\Http\Requests\Employee;

class UpdateEmployeeRequest extends StoreEmployeeRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('employees.edit');
    }
}
