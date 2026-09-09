<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'mobile' => $this->faker->numerify('9#########'),
            'email' => $this->faker->optional()->safeEmail(),
            'address' => $this->faker->address(),
            'milk_type' => $this->faker->randomElement(['cow', 'buffalo', 'mixed']),
            'morning_rate' => 0,
            'evening_rate' => 0,
            'default_qty_morning' => 1,
            'default_qty_evening' => 1,
            'status' => 'active',
            'joining_date' => now()->toDateString(),
            'opening_balance' => 0,
            'advance_balance' => 0,
            'outstanding_balance' => 0,
        ];
    }
}
