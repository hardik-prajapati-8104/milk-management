<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class DailyEntry extends Model
{
    use HasFactory;

    public const SHIFT_MORNING = 'morning';
    public const SHIFT_EVENING = 'evening';

    protected $fillable = [
        'customer_id', 'entry_date', 'shift', 'milk_type', 'quantity', 'rate', 'amount',
        'is_absent', 'is_holiday', 'is_manual_override', 'remarks',
        'entered_by', 'updated_by', 'is_locked', 'bill_id',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'quantity' => 'decimal:3',
            'rate' => 'decimal:2',
            'amount' => 'decimal:2',
            'is_absent' => 'boolean',
            'is_holiday' => 'boolean',
            'is_manual_override' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (DailyEntry $entry) {
            // Auto-calculate amount unless it's an absent/holiday zero entry.
            if ($entry->is_absent || $entry->is_holiday) {
                $entry->quantity = 0;
                $entry->amount = 0;
                return;
            }

            if (! $entry->is_manual_override) {
                $entry->amount = round((float) $entry->quantity * (float) $entry->rate, 2);
            }
        });

        static::updating(function (DailyEntry $entry) {
            // Locked entries can only be modified by admins; enforced additionally in FormRequest/Policy.
            if ($entry->getOriginal('is_locked') && ! $entry->is_manual_override) {
                if (! (auth()->check() && auth()->user()->hasRole('Admin'))) {
                    throw ValidationException::withMessages([
                        'entry' => 'This entry is locked and can only be edited by an Admin.',
                    ]);
                }
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('entry_date', $date);
    }

    public function scopeForShift($query, string $shift)
    {
        return $query->where('shift', $shift);
    }

    public function scopeUnbilled($query)
    {
        return $query->whereNull('bill_id');
    }
}
