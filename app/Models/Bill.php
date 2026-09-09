<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_number', 'invoice_number', 'customer_id', 'bill_month', 'bill_year',
        'period_start', 'period_end', 'morning_qty', 'evening_qty', 'total_qty',
        'milk_amount', 'extra_charges', 'delivery_charges', 'discount', 'penalty',
        'gst_percent', 'gst_amount', 'previous_due', 'advance_adjusted',
        'total_amount', 'paid_amount', 'outstanding_amount', 'status',
        'due_date', 'pdf_path', 'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'due_date' => 'date',
            'morning_qty' => 'decimal:3',
            'evening_qty' => 'decimal:3',
            'total_qty' => 'decimal:3',
            'milk_amount' => 'decimal:2',
            'extra_charges' => 'decimal:2',
            'delivery_charges' => 'decimal:2',
            'discount' => 'decimal:2',
            'penalty' => 'decimal:2',
            'gst_percent' => 'decimal:2',
            'gst_amount' => 'decimal:2',
            'previous_due' => 'decimal:2',
            'advance_adjusted' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'outstanding_amount' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    public function dailyEntries(): HasMany
    {
        return $this->hasMany(DailyEntry::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Recalculate the final total & outstanding based on current line amounts.
     * Called by BillingService after items/payments change.
     */
    public function recalculateTotals(): void
    {
        $this->total_qty = $this->morning_qty + $this->evening_qty;

        $gross = $this->milk_amount + $this->extra_charges + $this->delivery_charges
            + $this->penalty - $this->discount + $this->previous_due - $this->advance_adjusted;

        $this->gst_amount = round($gross * ((float) $this->gst_percent / 100), 2);
        $this->total_amount = round($gross + $this->gst_amount, 2);
        $this->outstanding_amount = round($this->total_amount - $this->paid_amount, 2);

        if ($this->outstanding_amount <= 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partially_paid';
        }
    }

    public static function generateBillNumber(int $month, int $year): string
    {
        $prefix = setting('invoice_prefix', 'INV');
        $sequence = static::where('bill_year', $year)->where('bill_month', $month)->count() + 1;

        return sprintf('%s-%04d%02d-%04d', $prefix, $year, $month, $sequence);
    }
}
