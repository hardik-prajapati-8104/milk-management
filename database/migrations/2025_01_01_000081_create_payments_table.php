<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('bill_id')->nullable()->constrained('bills')->nullOnDelete();
            $table->foreignId('payment_method_id')->constrained('payment_methods');

            $table->date('payment_date')->index();
            $table->decimal('amount', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('adjustment', 12, 2)->default(0);

            $table->string('reference_number')->nullable(); // cheque no / UPI ref / txn id
            $table->text('remarks')->nullable();

            $table->boolean('is_advance')->default(false);
            $table->enum('status', ['pending', 'cleared', 'bounced', 'cancelled'])->default('cleared');

            $table->string('receipt_pdf_path')->nullable();
            $table->boolean('sms_sent')->default(false);
            $table->boolean('whatsapp_sent')->default(false);

            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
