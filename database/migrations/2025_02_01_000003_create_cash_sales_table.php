<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_sales', function (Blueprint $table) {
            $table->id();
            $table->string('cash_sale_no')->unique(); // CASH-000025

            $table->date('sale_date')->index();
            $table->enum('shift', ['morning', 'evening'])->index();

            $table->enum('milk_type', ['cow', 'buffalo', 'mixed'])->default('cow');
            $table->decimal('quantity', 10, 3);
            $table->decimal('total_amount', 12, 2);

            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['sale_date', 'shift']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_sales');
    }
};
