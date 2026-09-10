<?php

namespace App\Models;

use App\Support\UserAgentParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'log_type', 'action', 'subject_type', 'subject_id',
        'old_values', 'new_values', 'description', 'ip_address', 'user_agent',
        'method', 'url', 'browser', 'browser_version', 'platform', 'device_type',
        'session_id',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Record an activity/audit entry. This is the single choke point every
     * ActivityLog write goes through (controllers, services, middleware,
     * observers), so request/device context is captured consistently
     * everywhere without touching each call site individually.
     */
    public static function record(string $action, ?Model $subject = null, array $old = [], array $new = [], ?string $description = null, string $logType = 'activity'): self
    {
        $request = request();
        $agent = UserAgentParser::parse($request?->userAgent());

        return static::create([
            'user_id' => auth()->id(),
            'log_type' => $logType,
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'old_values' => $old,
            'new_values' => $new,
            'description' => $description,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'method' => $request?->method(),
            'url' => $request ? substr($request->fullUrl(), 0, 2048) : null,
            'browser' => $agent['browser'],
            'browser_version' => $agent['browser_version'],
            'platform' => $agent['platform'],
            'device_type' => $agent['device_type'],
            'session_id' => $request?->hasSession() ? $request->session()->getId() : null,
        ]);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('log_type', $type);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeFromIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }
}
