<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\CashSale;
use Illuminate\Support\Facades\DB;

class CashSaleService
{
    public function __construct(protected MilkStockService $stock)
    {
    }

    /**
     * @throws \RuntimeException when the quantity exceeds available milk stock
     */
    public function create(array $data): CashSale
    {
        return DB::transaction(function () use ($data) {
            $data['cash_sale_no'] = CashSale::generateCashSaleNo();
            $data['created_by'] = auth()->id();

            $sale = CashSale::create($data);

            $this->stock->recordMovement(
                code: $sale->cash_sale_no,
                date: $sale->sale_date->toDateString(),
                shift: $sale->shift,
                direction: 'out',
                movementType: 'cash_sale',
                quantity: (float) $sale->quantity,
                rate: $sale->effectiveRate(),
                source: $sale,
                remarks: 'Cash sale (walk-in customers)',
            );

            ActivityLog::record('created', $sale, new: $sale->toArray(),
                description: "Cash sale {$sale->cash_sale_no}: {$sale->quantity}L for " . money($sale->total_amount));

            return $sale->fresh();
        });
    }
}
