<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\DailyEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyEntryFactory extends Factory
{
    protected $model = DailyEntry::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'entry_date' => now()->toDateString(),
            'shift' => 'morning',
            'milk_type' => 'cow',
            'quantity' => 1,
            'rate' => 50,
            'is_absent' => false,
            'is_holiday' => false,
            'is_manual_override' => false,
            'is_locked' => false,
        ];
    }
}
