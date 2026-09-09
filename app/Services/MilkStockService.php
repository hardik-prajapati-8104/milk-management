<?php

namespace App\Services;

use App\Models\MilkStockLedger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MilkStockService
{
    /**
     * Record a single stock movement against the ledger, atomically, with a
     * row lock on the last ledger entry so concurrent purchases/sales never
     * race on the running balance. Outbound movements are rejected outright
     * if they would take stock negative — this is the one rule the whole
     * "never sell more milk than available" requirement hangs on.
     *
     * @throws \RuntimeException when an outbound movement exceeds available stock
     */
    public function recordMovement(
        string $code,
        string $date,
        string $shift,
        string $direction,
        string $movementType,
        float $quantity,
        float $rate = 0,
        ?Model $source = null,
        ?string $remarks = null,
    ): MilkStockLedger {
        return DB::transaction(function () use ($code, $date, $shift, $direction, $movementType, $quantity, $rate, $source, $remarks) {
            // Lock the most recent ledger row so two simultaneous sales can't
            // both read the same "before" balance and oversell the same milk.
            $last = MilkStockLedger::lockForUpdate()->orderByDesc('id')->first();
            $stockBefore = (float) ($last->stock_after ?? 0);

            if ($direction === 'out' && $quantity > $stockBefore) {
                throw new \RuntimeException(
                    "Insufficient milk stock. Available quantity: {$this->formatQty($stockBefore)} L"
                );
            }

            $stockAfter = $direction === 'in' ? $stockBefore + $quantity : $stockBefore - $quantity;

            return MilkStockLedger::create([
                'code' => $code,
                'movement_date' => $date,
                'shift' => $shift,
                'direction' => $direction,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'rate' => $rate,
                'amount' => round($quantity * $rate, 2),
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'source_type' => $source ? get_class($source) : null,
                'source_id' => $source?->getKey(),
                'remarks' => $remarks,
                'created_by' => auth()->id(),
            ]);
        });
    }

    /**
     * Check whether a given quantity could currently be sold without
     * throwing — used by form validation before attempting the write.
     */
    public function canSell(float $quantity): bool
    {
        return $quantity <= MilkStockLedger::currentBalance();
    }

    public function currentStock(): float
    {
        return MilkStockLedger::currentBalance();
    }

    /**
     * Full opening -> incoming -> outgoing -> closing breakdown for one date
     * (optionally scoped to a single shift), matching the "Daily Closing"
     * and shift-management summary shapes from the spec.
     */
    public function dayBreakdown(string $date, ?string $shift = null): array
    {
        $query = MilkStockLedger::where('movement_date', $date);
        if ($shift) {
            $query->where('shift', $shift);
        }
        $rows = $query->get();

        $opening = MilkStockLedger::where('movement_date', '<', $date)
            ->orderByDesc('movement_date')->orderByDesc('id')->value('stock_after') ?? 0;

        $incoming = (float) $rows->where('movement_type', 'purchase')->sum('quantity');
        $cardSales = (float) $rows->where('movement_type', 'card_sale')->sum('quantity');
        $cashSales = (float) $rows->where('movement_type', 'cash_sale')->sum('quantity');
        $dairySales = (float) $rows->where('movement_type', 'dairy_sale')->sum('quantity');
        $wastage = (float) $rows->where('movement_type', 'wastage')->sum('quantity');
        $adjustment = (float) $rows->where('movement_type', 'adjustment')->sum('quantity');

        $closing = (float) $opening + $incoming + $adjustment - $cardSales - $cashSales - $dairySales - $wastage;

        return compact('opening', 'incoming', 'cardSales', 'cashSales', 'dairySales', 'wastage', 'adjustment', 'closing');
    }

    protected function formatQty(float $qty): string
    {
        return rtrim(rtrim(number_format($qty, 3, '.', ''), '0'), '.');
    }
}
