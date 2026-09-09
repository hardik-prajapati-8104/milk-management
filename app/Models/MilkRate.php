<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MilkRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'effective_date', 'cow_morning_rate', 'cow_evening_rate',
        'buffalo_morning_rate', 'buffalo_evening_rate', 'mixed_rate',
        'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Resolve the applicable rate for a given date, milk type, and shift,
     * based on the most recent rate card effective on or before that date.
     */
    public static function resolveRate(string $date, string $milkType, string $shift): float
    {
        $rateCard = static::where('is_active', true)
            ->where('effective_date', '<=', $date)
            ->orderByDesc('effective_date')
            ->first();

        if (! $rateCard) {
            return 0;
        }

        if ($milkType === 'mixed') {
            return (float) $rateCard->mixed_rate;
        }

        $column = $milkType . '_' . $shift . '_rate'; // e.g. cow_morning_rate

        return (float) ($rateCard->{$column} ?? 0);
    }
}
