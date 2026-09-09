<?php

namespace Database\Factories;

use App\Models\MilkRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class MilkRateFactory extends Factory
{
    protected $model = MilkRate::class;

    public function definition(): array
    {
        return [
            'effective_date' => now()->subDays(30)->toDateString(),
            'cow_morning_rate' => 50,
            'cow_evening_rate' => 52,
            'buffalo_morning_rate' => 60,
            'buffalo_evening_rate' => 62,
            'mixed_rate' => 55,
            'is_active' => true,
        ];
    }
}
