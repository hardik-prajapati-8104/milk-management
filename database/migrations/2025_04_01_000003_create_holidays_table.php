<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->date('date')->index();
            $table->enum('type', ['national', 'regional', 'company', 'optional'])->default('company');
            // When true, this holiday is treated as falling on the same month/day every year.
            $table->boolean('is_recurring_yearly')->default(true);
            $table->string('color', 7)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
