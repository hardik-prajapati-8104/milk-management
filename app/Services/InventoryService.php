<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Record a stock movement and atomically adjust the product's current_stock.
     * Inbound types (collection, purchase, transfer_in, adjustment) increase stock;
     * outbound types (sale, wastage, transfer_out) decrease it. A negative result
     * is blocked for outbound movements to avoid stock going below zero by mistake.
     */
    public function recordMovement(array $data): InventoryMovement
    {
        return DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            $isInbound = in_array($data['type'], InventoryMovement::IN_TYPES, true) || $data['type'] === 'adjustment';
            $quantity = (float) $data['quantity'];
            $stockBefore = (float) $product->current_stock;

            if (! $isInbound && $quantity > $stockBefore) {
                throw new \RuntimeException("Insufficient stock: only {$stockBefore} {$product->unit} available.");
            }

            $stockAfter = $isInbound ? $stockBefore + $quantity : $stockBefore - $quantity;
            $rate = (float) ($data['rate'] ?? ($isInbound ? $product->purchase_price : $product->selling_price));

            $movement = InventoryMovement::create($data + [
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'rate' => $rate,
                'amount' => round($quantity * $rate, 2),
                'created_by' => auth()->id(),
            ]);

            $product->update(['current_stock' => $stockAfter]);

            if ($product->isLowStock()) {
                ActivityLog::record('low-stock', $product, description: "{$product->name} is running low: {$stockAfter} {$product->unit} remaining");
            }

            if (! empty($data['supplier_id']) && in_array($data['type'], ['purchase', 'collection'], true)) {
                Supplier::find($data['supplier_id'])?->increment('outstanding_balance', $movement->amount);
            }

            ActivityLog::record('created', $movement,
                description: ucfirst($data['type']) . " of {$quantity} {$product->unit} {$product->name} recorded");

            return $movement;
        });
    }
}
