<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    public const IN_TYPES = ['collection', 'purchase', 'transfer_in'];
    public const OUT_TYPES = ['sale', 'wastage', 'transfer_out'];

    protected $fillable = [
        'product_id', 'supplier_id', 'movement_date', 'type', 'quantity',
        'rate', 'amount', 'stock_before', 'stock_after', 'remarks', 'created_by',
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isInbound(): bool
    {
        return in_array($this->type, self::IN_TYPES, true) || $this->type === 'adjustment';
    }
}
