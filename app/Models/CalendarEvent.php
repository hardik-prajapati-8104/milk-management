<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class CalendarEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type', 'title', 'description', 'start_datetime', 'end_datetime', 'all_day',
        'location', 'color', 'status', 'priority', 'reminder_minutes_before', 'reminder_sent',
        'is_recurring', 'recurrence_type', 'recurrence_end_date', 'recurrence_parent_id',
        'assigned_to', 'created_by', 'related_type', 'related_id',
    ];

    /** Default colour per type, used whenever a record doesn't set its own `color`. */
    public const TYPE_COLORS = [
        'event' => '#0d6efd',     // blue
        'meeting' => '#6f42c1',   // purple
        'reminder' => '#fd7e14',  // orange
        'task' => '#198754',      // green
    ];

    public const STATUS_LABELS = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'all_day' => 'boolean',
            'reminder_sent' => 'boolean',
            'is_recurring' => 'boolean',
            'recurrence_end_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CalendarEvent $event) {
            if (empty($event->created_by) && auth()->check()) {
                $event->created_by = auth()->id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'calendar_event_attendees')
            ->withPivot('response_status')
            ->withTimestamps();
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeEvents(Builder $query): Builder
    {
        return $query->where('type', 'event');
    }

    public function scopeMeetings(Builder $query): Builder
    {
        return $query->where('type', 'meeting');
    }

    public function scopeReminders(Builder $query): Builder
    {
        return $query->where('type', 'reminder');
    }

    public function scopeTasks(Builder $query): Builder
    {
        return $query->where('type', 'task');
    }

    /** Anything overlapping the given [start, end] window, for a calendar feed. */
    public function scopeBetween(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->where('start_datetime', '<=', $end)
            ->where(function (Builder $q) use ($start) {
                $q->where('end_datetime', '>=', $start)
                    ->orWhereNull('end_datetime')
                    ->orWhere('start_datetime', '>=', $start);
            });
    }

    public function scopeUpcoming(Builder $query, int $days = 7): Builder
    {
        return $query->whereBetween('start_datetime', [now(), now()->addDays($days)])
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function getColorAttribute($value)
    {
        return $value ?: (self::TYPE_COLORS[$this->type] ?? '#6c757d');
    }

    public function isOverdue(): bool
    {
        return $this->type === 'task'
            && $this->status !== 'completed'
            && $this->status !== 'cancelled'
            && $this->start_datetime?->isPast();
    }
}
