<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Birthday extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'date_of_birth', 'category', 'related_type', 'related_id',
        'photo', 'mobile', 'notes', 'reminder_days_before', 'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Birthday $birthday) {
            if (empty($birthday->created_by) && auth()->check()) {
                $birthday->created_by = auth()->id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /** This year's (or next year's, if already passed) occurrence of the birthday. */
    public function nextOccurrence(): Carbon
    {
        $today = Carbon::today();

        try {
            $thisYear = Carbon::create($today->year, $this->date_of_birth->month, $this->date_of_birth->day);
        } catch (\Throwable) {
            $thisYear = Carbon::create($today->year, 3, 1); // Feb 29 fallback
        }

        return $thisYear->isBefore($today) ? $thisYear->addYear() : $thisYear;
    }

    public function turningAge(): int
    {
        return $this->nextOccurrence()->year - $this->date_of_birth->year;
    }

    public function daysUntil(): int
    {
        return Carbon::today()->diffInDays($this->nextOccurrence(), false);
    }
}
