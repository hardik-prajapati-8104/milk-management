<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'date', 'type', 'is_recurring_yearly', 'color', 'description', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_recurring_yearly' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Holiday $holiday) {
            if (empty($holiday->created_by) && auth()->check()) {
                $holiday->created_by = auth()->id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Resolve this holiday's occurrence date within the given year — for
     * recurring-yearly holidays this re-maps month/day onto $year, for
     * fixed one-off holidays it just returns the stored date (or null if
     * the stored date isn't in that year).
     */
    public function occurrenceInYear(int $year): ?Carbon
    {
        if ($this->is_recurring_yearly) {
            try {
                return Carbon::create($year, $this->date->month, $this->date->day);
            } catch (\Throwable) {
                return null; // e.g. Feb 29 on a non-leap year
            }
        }

        return $this->date->year === $year ? $this->date->copy() : null;
    }

    public function scopeUpcoming(Builder $query, int $days = 30): Builder
    {
        return $query; // filtered in the service layer since recurrence needs PHP-side date math
    }
}
