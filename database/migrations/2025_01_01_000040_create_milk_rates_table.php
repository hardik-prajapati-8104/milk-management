<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milk_rates', function (Blueprint $table) {
            $table->id();
            $table->date('effective_date')->index();
            $table->decimal('cow_morning_rate', 8, 2)->default(0);
            $table->decimal('cow_evening_rate', 8, 2)->default(0);
            $table->decimal('buffalo_morning_rate', 8, 2)->default(0);
            $table->decimal('buffalo_evening_rate', 8, 2)->default(0);
            $table->decimal('mixed_rate', 8, 2)->default(0);
            // Optional: per-customer rate override is stored on customers table directly.
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milk_rates');
    }
};
