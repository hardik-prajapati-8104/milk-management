<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;

class MilkPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'incoming_no', 'purchase_date', 'purchase_time', 'shift',
        'supplier_id', 'milk_type', 'quantity', 'rate', 'total_amount',
        'fat_percent', 'snf_percent', 'quality_grade',
        'payment_status', 'payment_method_id', 'paid_amount',
        'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'quantity' => 'decimal:3',
            'rate' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'fat_percent' => 'decimal:2',
            'snf_percent' => 'decimal:2',
            'paid_amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (MilkPurchase $purchase) {
            $purchase->total_amount = round((float) $purchase->quantity * (float) $purchase->rate, 2);
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
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

    public function outstandingAmount(): float
    {
        return round((float) $this->total_amount - (float) $this->paid_amount, 2);
    }

    public static function generateIncomingNo(): string
    {
        return DB::transaction(function () {
            $last = static::lockForUpdate()->orderByDesc('id')->first();
            $next = 1;
            if ($last && preg_match('/(\d+)$/', $last->incoming_no, $m)) {
                $next = ((int) $m[1]) + 1;
            }

            return 'IN-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
        });
    }
}
