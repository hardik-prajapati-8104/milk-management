<?php

namespace App\Http\Requests\Expense;

class UpdateExpenseRequest extends StoreExpenseRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('expenses.edit');
    }
}
