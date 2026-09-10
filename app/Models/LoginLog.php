<?php

namespace App\Models;

use App\Support\UserAgentParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class LoginLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'email', 'status', 'reason', 'ip_address', 'user_agent',
        'browser', 'browser_version', 'platform', 'device_type', 'session_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  'success'|'failed'|'locked_out'|'logged_out'  $status
     */
    public static function record(string $status, ?User $user = null, ?string $email = null, ?string $reason = null, ?Request $request = null): self
    {
        $request ??= request();
        $agent = UserAgentParser::parse($request?->userAgent());

        return static::create([
            'user_id' => $user?->id,
            'email' => $email ?? $user?->email,
            'status' => $status,
            'reason' => $reason,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'browser' => $agent['browser'],
            'browser_version' => $agent['browser_version'],
            'platform' => $agent['platform'],
            'device_type' => $agent['device_type'],
            'session_id' => $request?->hasSession() ? $request->session()->getId() : null,
            'created_at' => now(),
        ]);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
