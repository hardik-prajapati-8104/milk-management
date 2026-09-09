<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_entries', function (Blueprint $table) {
            $table->foreign('bill_id')->references('id')->on('bills')->nullOnDelete();
            $table->index('bill_id');
        });
    }

    public function down(): void
    {
        Schema::table('daily_entries', function (Blueprint $table) {
            $table->dropForeign(['bill_id']);
        });
    }
};
