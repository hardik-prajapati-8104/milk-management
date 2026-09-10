<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Manually-entered birthdays (staff, contacts, VIP customers, family of
     * staff, etc). Employees/customers that already have a date_of_birth on
     * their own record are pulled onto the calendar automatically by
     * BirthdayService without needing a row here — this table is for
     * anyone else, plus it doubles as an override/annotation source.
     */
    public function up(): void
    {
        Schema::create('birthdays', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->date('date_of_birth');
            $table->enum('category', ['employee', 'customer', 'supplier', 'other'])->default('other');
            $table->nullableMorphs('related');
            $table->string('photo')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('reminder_days_before')->default(1);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthdays');
    }
};
