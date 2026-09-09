<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Validators\Failure;

class CustomersImport implements OnEachRow, SkipsOnError, SkipsOnFailure, WithHeadingRow, WithValidation
{
    use Importable;

    public int $imported = 0;

    protected array $errors = [];

    public function onRow(Row $row): void
    {
        $data = $row->toArray();

        // Consumer ID is always auto-generated on import; even if the sheet
        // includes an old ID from another system, we never trust it directly.
        Customer::create([
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'pincode' => $data['pincode'] ?? null,
            'milk_type' => strtolower($data['milk_type'] ?? 'cow'),
            'morning_rate' => $data['morning_rate'] ?? 0,
            'evening_rate' => $data['evening_rate'] ?? 0,
            'default_qty_morning' => $data['default_qty_morning'] ?? 0,
            'default_qty_evening' => $data['default_qty_evening'] ?? 0,
            'status' => 'active',
            'joining_date' => $data['joining_date'] ?? now()->toDateString(),
            'created_by' => auth()->id(),
        ]);

        $this->imported++;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'mobile' => ['required', 'string', 'max:15'],
            'email' => ['nullable', 'email'],
            'milk_type' => ['nullable', 'in:cow,buffalo,mixed,Cow,Buffalo,Mixed'],
            'morning_rate' => ['nullable', 'numeric', 'min:0'],
            'evening_rate' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function onError(\Throwable $e): void
    {
        $this->errors[] = $e->getMessage();
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
