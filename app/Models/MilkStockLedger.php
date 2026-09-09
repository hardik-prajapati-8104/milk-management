<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MilkStockLedger extends Model
{
    protected $table = 'milk_stock_ledger';

    public const IN_TYPES = ['purchase', 'adjustment'];
    public const OUT_TYPES = ['card_sale', 'cash_sale', 'dairy_sale', 'wastage'];

    protected $fillable = [
        'code', 'movement_date', 'shift', 'direction', 'movement_type',
        'quantity', 'rate', 'amount', 'stock_before', 'stock_after',
        'source_type', 'source_id', 'remarks', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'movement_date' => 'date',
            'quantity' => 'decimal:3',
            'rate' => 'decimal:2',
            'amount' => 'decimal:2',
            'stock_before' => 'decimal:3',
            'stock_after' => 'decimal:3',
        ];
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The current available milk stock is simply the stock_after of the
     * most recent ledger row — every entry already carries the running
     * balance forward, so no separate aggregate/cache table is needed.
     */
    public static function currentBalance(): float
    {
        return (float) (static::orderByDesc('id')->value('stock_after') ?? 0);
    }

    /**
     * The stock balance as it stood at the end of a given date (and, if given,
     * at the end of a given shift within that date). Used by shift-wise and
     * date-wise stock reports.
     */
    public static function balanceAsOf(string $date, ?string $shift = null): float
    {
        $query = static::where(function ($q) use ($date, $shift) {
            $q->where('movement_date', '<', $date)
                ->orWhere(function ($q2) use ($date, $shift) {
                    $q2->where('movement_date', $date);
                    if ($shift === 'morning') {
                        $q2->where('shift', 'morning');
                    }
                    // shift === 'evening' or null: no further restriction, include the whole day
                });
        });

        return (float) ($query->orderByDesc('movement_date')->orderByDesc('id')->value('stock_after') ?? 0);
    }

    /**
     * Sequential OUT-000001 style code shared by Card Customer sales and
     * Dairy/Amul sales, matching the spec's single outgoing-transaction
     * sequence. Cash Sales use their own CASH-xxxxxx numbering instead
     * (see CashSale::generateCashSaleNo()) since the spec treats them as
     * a distinct document series.
     */
    public static function generateOutgoingNo(): string
    {
        return \Illuminate\Support\Facades\DB::transaction(function () {
            $last = static::where('code', 'like', 'OUT-%')->lockForUpdate()->orderByDesc('id')->first();
            $next = 1;
            if ($last && preg_match('/(\d+)$/', $last->code, $m)) {
                $next = ((int) $m[1]) + 1;
            }

            return 'OUT-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
        });
    }
}
