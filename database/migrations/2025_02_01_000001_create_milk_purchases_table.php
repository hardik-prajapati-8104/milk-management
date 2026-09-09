<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milk_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('incoming_no')->unique(); // IN-000001

            $table->date('purchase_date')->index();
            $table->time('purchase_time')->nullable();
            $table->enum('shift', ['morning', 'evening'])->index();

            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->enum('milk_type', ['cow', 'buffalo', 'mixed'])->default('cow');

            $table->decimal('quantity', 10, 3);
            $table->decimal('rate', 10, 2);
            $table->decimal('total_amount', 12, 2);

            $table->decimal('fat_percent', 5, 2)->nullable();
            $table->decimal('snf_percent', 5, 2)->nullable();
            $table->string('quality_grade', 20)->nullable();

            $table->enum('payment_status', ['paid', 'partial', 'pending'])->default('pending')->index();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->decimal('paid_amount', 12, 2)->default(0);

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['purchase_date', 'shift']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milk_purchases');
    }
};
