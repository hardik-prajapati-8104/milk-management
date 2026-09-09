<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct(protected array $filters = [])
    {
    }

    public function query(): Builder
    {
        $query = Customer::query()->with(['village', 'area', 'route', 'category']);

        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (! empty($this->filters['route_id'])) {
            $query->where('route_id', $this->filters['route_id']);
        }
        if (! empty($this->filters['search'])) {
            $query->search($this->filters['search']);
        }

        return $query->orderBy('consumer_id');
    }

    public function headings(): array
    {
        return [
            'Consumer ID', 'Name', 'Mobile', 'Email', 'Address', 'Village', 'Area',
            'Route', 'Milk Type', 'Morning Rate', 'Evening Rate', 'Category',
            'Status', 'Outstanding Balance', 'Joining Date',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->consumer_id,
            $customer->name,
            $customer->mobile,
            $customer->email,
            $customer->address,
            $customer->village->name ?? '',
            $customer->area->name ?? '',
            $customer->route->name ?? '',
            ucfirst($customer->milk_type),
            $customer->morning_rate,
            $customer->evening_rate,
            $customer->category->name ?? '',
            ucfirst($customer->status),
            $customer->outstanding_balance,
            optional($customer->joining_date)->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
