<?php

namespace Database\Seeders;

use App\Models\CustomerCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Residential', 'Commercial', 'Hotel', 'Restaurant', 'Shop', 'Office'] as $name) {
            CustomerCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );
        }
    }
}
