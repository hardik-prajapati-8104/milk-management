<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('consumer_id')->unique(); // MILK00001, MILK00002...
            $table->string('name');
            $table->string('photo')->nullable();
            $table->string('mobile', 15)->index();
            $table->string('alternative_mobile', 15)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->foreignId('route_id')->nullable()->constrained('routes')->nullOnDelete();

            $table->enum('milk_type', ['cow', 'buffalo', 'mixed'])->default('cow');
            $table->decimal('morning_rate', 8, 2)->default(0);
            $table->decimal('evening_rate', 8, 2)->default(0);
            $table->decimal('default_qty_morning', 8, 3)->default(0);
            $table->decimal('default_qty_evening', 8, 3)->default(0);

            $table->foreignId('customer_category_id')->nullable()->constrained('customer_categories')->nullOnDelete();

            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->date('joining_date')->nullable();

            $table->string('identity_proof_type')->nullable();
            $table->string('identity_proof_number')->nullable();

            $table->string('qr_code_path')->nullable();
            $table->string('barcode')->unique()->nullable();

            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('advance_balance', 12, 2)->default(0);
            $table->decimal('outstanding_balance', 12, 2)->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['milk_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
