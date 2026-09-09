<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;

class CashSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_sale_no', 'sale_date', 'shift', 'milk_type', 'quantity',
        'total_amount', 'payment_method_id', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'quantity' => 'decimal:3',
            'total_amount' => 'decimal:2',
        ];
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ledgerEntry(): MorphOne
    {
        return $this->morphOne(MilkStockLedger::class, 'source');
    }

    /**
     * The effective per-liter rate, derived from quantity/total rather than
     * stored directly — cash sales are recorded as "X liters for ₹Y", matching
     * how a shopkeeper actually rings up a walk-in sale.
     */
    public function effectiveRate(): float
    {
        return (float) $this->quantity > 0 ? round((float) $this->total_amount / (float) $this->quantity, 2) : 0.0;
    }

    public static function generateCashSaleNo(): string
    {
        return DB::transaction(function () {
            $last = static::lockForUpdate()->orderByDesc('id')->first();
            $next = 1;
            if ($last && preg_match('/(\d+)$/', $last->cash_sale_no, $m)) {
                $next = ((int) $m[1]) + 1;
            }

            return 'CASH-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
        });
    }
}
