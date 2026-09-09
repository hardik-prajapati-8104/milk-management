<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericTableExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    /**
     * @param  string[]  $headings
     * @param  array<int, array<int, mixed>>  $rows
     */
    public function __construct(
        protected array $headings,
        protected array $rows,
        protected string $title = 'Report',
    ) {
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return substr($this->title, 0, 31); // Excel sheet name limit
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
