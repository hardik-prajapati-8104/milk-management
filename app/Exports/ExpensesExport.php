<?php

namespace App\Exports;

use App\Models\Expense;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct(protected array $filters = [])
    {
    }

    public function query(): Builder
    {
        $query = Expense::query()->with(['category', 'paymentMethod']);

        if (! empty($this->filters['category_id'])) {
            $query->where('expense_category_id', $this->filters['category_id']);
        }
        if (! empty($this->filters['from'])) {
            $query->whereDate('expense_date', '>=', $this->filters['from']);
        }
        if (! empty($this->filters['to'])) {
            $query->whereDate('expense_date', '<=', $this->filters['to']);
        }

        return $query->orderByDesc('expense_date');
    }

    public function headings(): array
    {
        return ['Expense #', 'Date', 'Category', 'Amount', 'Paid To', 'Payment Method', 'Reference', 'Remarks'];
    }

    public function map($expense): array
    {
        return [
            $expense->expense_number,
            $expense->expense_date->format('Y-m-d'),
            $expense->category->name ?? '',
            $expense->amount,
            $expense->paid_to,
            $expense->paymentMethod->name ?? '',
            $expense->reference_number,
            $expense->remarks,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
