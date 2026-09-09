<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number', 'customer_id', 'bill_id', 'payment_method_id',
        'payment_date', 'amount', 'discount', 'adjustment', 'reference_number',
        'remarks', 'is_advance', 'status', 'receipt_pdf_path',
        'sms_sent', 'whatsapp_sent', 'received_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
            'discount' => 'decimal:2',
            'adjustment' => 'decimal:2',
            'is_advance' => 'boolean',
            'sms_sent' => 'boolean',
            'whatsapp_sent' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = static::generateReceiptNumber();
            }
        });
    }

    public static function generateReceiptNumber(): string
    {
        $prefix = setting('receipt_prefix', 'RCPT');
        $date = now()->format('Ymd');
        $sequence = static::whereDate('created_at', now()->toDateString())->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function netAmount(): float
    {
        return (float) $this->amount - (float) $this->discount + (float) $this->adjustment;
    }
}
