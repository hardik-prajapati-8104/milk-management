<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Milk Purchase', 'Salary', 'Fuel', 'Electricity',
            'Maintenance', 'Vehicle', 'Packaging', 'Miscellaneous',
        ] as $name) {
            ExpenseCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );
        }
    }
}
