<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milk_stock_ledger', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // mirrors the source document's code (IN-xxx, OUT-xxx, CASH-xxx)

            $table->date('movement_date')->index();
            $table->enum('shift', ['morning', 'evening'])->index();

            $table->enum('direction', ['in', 'out'])->index();
            $table->enum('movement_type', [
                'purchase', 'card_sale', 'cash_sale', 'dairy_sale', 'wastage', 'adjustment',
            ])->index();

            $table->decimal('quantity', 10, 3);
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);

            $table->decimal('stock_before', 10, 3);
            $table->decimal('stock_after', 10, 3);

            // Polymorphic link back to the source document (MilkPurchase, DailyEntry, CashSale, DairySale, etc.)
            $table->nullableMorphs('source');

            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['movement_date', 'shift', 'direction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milk_stock_ledger');
    }
};
