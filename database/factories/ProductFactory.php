<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Milk', 'Curd', 'Paneer', 'Ghee']),
            'sku' => 'SKU-' . $this->faker->unique()->numberBetween(1, 999999),
            'unit' => 'litre',
            'purchase_price' => 30,
            'selling_price' => 40,
            'current_stock' => 0,
            'low_stock_threshold' => 5,
            'is_active' => true,
        ];
    }
}
