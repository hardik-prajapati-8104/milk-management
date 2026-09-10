<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Throwable;

class ErrorLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'level', 'exception_class', 'message', 'file', 'line', 'trace',
        'method', 'url', 'user_id', 'ip_address', 'user_agent', 'is_resolved',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'is_resolved' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(Throwable $e, ?Request $request = null): self
    {
        $request ??= (app()->bound('request') ? request() : null);

        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        $level = match (true) {
            $statusCode >= 500 || ! method_exists($e, 'getStatusCode') => 'critical',
            $statusCode >= 400 => 'warning',
            default => 'error',
        };

        return static::create([
            'level' => $level,
            'exception_class' => get_class($e),
            'message' => mb_substr($e->getMessage() ?: '(no message)', 0, 5000),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => mb_substr($e->getTraceAsString(), 0, 20000),
            'method' => $request?->method(),
            'url' => $request ? mb_substr($request->fullUrl(), 0, 2048) : null,
            'user_id' => auth()->id(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'is_resolved' => false,
            'created_at' => now(),
        ]);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }
}
