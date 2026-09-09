<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class BillFactory extends Factory
{
    protected $model = Bill::class;

    public function definition(): array
    {
        $month = now()->month;
        $year = now()->year;

        return [
            'bill_number' => 'INV-' . $this->faker->unique()->numberBetween(1, 999999),
            'invoice_number' => 'INV/' . $this->faker->unique()->numberBetween(1, 999999),
            'customer_id' => Customer::factory(),
            'bill_month' => $month,
            'bill_year' => $year,
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'morning_qty' => 0,
            'evening_qty' => 0,
            'total_qty' => 0,
            'milk_amount' => 0,
            'total_amount' => 0,
            'paid_amount' => 0,
            'outstanding_amount' => 0,
            'status' => 'draft',
        ];
    }
}
