<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();

            $table->unsignedTinyInteger('bill_month'); // 1-12
            $table->unsignedSmallInteger('bill_year');
            $table->date('period_start');
            $table->date('period_end');

            $table->decimal('morning_qty', 10, 3)->default(0);
            $table->decimal('evening_qty', 10, 3)->default(0);
            $table->decimal('total_qty', 10, 3)->default(0);

            $table->decimal('milk_amount', 12, 2)->default(0);
            $table->decimal('extra_charges', 12, 2)->default(0);
            $table->decimal('delivery_charges', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('penalty', 12, 2)->default(0);
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->decimal('gst_amount', 12, 2)->default(0);

            $table->decimal('previous_due', 12, 2)->default(0);
            $table->decimal('advance_adjusted', 12, 2)->default(0);

            $table->decimal('total_amount', 12, 2)->default(0); // final amount for this bill
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('outstanding_amount', 12, 2)->default(0);

            $table->enum('status', ['draft', 'generated', 'partially_paid', 'paid', 'cancelled'])->default('draft')->index();

            $table->date('due_date')->nullable();
            $table->string('pdf_path')->nullable();

            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['customer_id', 'bill_month', 'bill_year'], 'uniq_customer_bill_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
