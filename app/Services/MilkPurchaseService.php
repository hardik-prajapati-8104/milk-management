<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\MilkPurchase;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class MilkPurchaseService
{
    public function __construct(protected MilkStockService $stock)
    {
    }

    /**
     * Record an incoming milk purchase: creates the purchase document,
     * increases the shared milk stock ledger, and — if not fully paid —
     * increases the supplier's outstanding balance.
     */
    public function create(array $data): MilkPurchase
    {
        return DB::transaction(function () use ($data) {
            $data['incoming_no'] = MilkPurchase::generateIncomingNo();
            $data['created_by'] = auth()->id();
            $data['paid_amount'] = $data['payment_status'] === 'paid'
                ? round((float) $data['quantity'] * (float) $data['rate'], 2)
                : (float) ($data['paid_amount'] ?? 0);

            $purchase = MilkPurchase::create($data);

            $this->stock->recordMovement(
                code: $purchase->incoming_no,
                date: $purchase->purchase_date->toDateString(),
                shift: $purchase->shift,
                direction: 'in',
                movementType: 'purchase',
                quantity: (float) $purchase->quantity,
                rate: (float) $purchase->rate,
                source: $purchase,
                remarks: "Purchase from {$purchase->supplier->name}",
            );

            $outstanding = $purchase->outstandingAmount();
            if ($outstanding > 0) {
                Supplier::whereKey($purchase->supplier_id)->increment('outstanding_balance', $outstanding);
            }

            ActivityLog::record('created', $purchase, new: $purchase->toArray(),
                description: "Incoming milk {$purchase->incoming_no}: {$purchase->quantity}L from {$purchase->supplier->name}");

            return $purchase->fresh();
        });
    }

    /**
     * Record an additional payment against a purchase already on credit,
     * mirroring the supplier's outstanding balance back down.
     */
    public function recordPayment(MilkPurchase $purchase, float $amount): MilkPurchase
    {
        return DB::transaction(function () use ($purchase, $amount) {
            $before = $purchase->outstandingAmount();
            $purchase->increment('paid_amount', $amount);
            $purchase->refresh();

            $purchase->payment_status = $purchase->outstandingAmount() <= 0 ? 'paid' : 'partial';
            $purchase->save();

            $applied = min($amount, $before);
            Supplier::whereKey($purchase->supplier_id)->decrement('outstanding_balance', $applied);

            ActivityLog::record('updated', $purchase, description: "Payment of {$amount} recorded against {$purchase->incoming_no}");

            return $purchase;
        });
    }
}
