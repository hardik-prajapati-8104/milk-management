<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->date('entry_date')->index();
            $table->enum('shift', ['morning', 'evening'])->index();

            $table->enum('milk_type', ['cow', 'buffalo', 'mixed'])->default('cow');
            $table->decimal('quantity', 8, 3)->default(0);
            $table->decimal('rate', 8, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);

            $table->boolean('is_absent')->default(false);
            $table->boolean('is_holiday')->default(false);
            $table->boolean('is_manual_override')->default(false);

            $table->text('remarks')->nullable();

            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->boolean('is_locked')->default(false); // locked once billed / past days, editable by admin only

            $table->unsignedBigInteger('bill_id')->nullable(); // FK added after bills table exists

            $table->timestamps();

            // one entry per customer per shift per day
            $table->unique(['customer_id', 'entry_date', 'shift'], 'uniq_customer_date_shift');
            $table->index(['entry_date', 'shift']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_entries');
    }
};
