<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogApiActivity
{
    /**
     * Fields that must never be persisted, even inside the logged request payload.
     */
    protected array $redactedFields = [
        'password', 'password_confirmation', 'token', 'api_token', 'secret',
        'authorization', 'card_number', 'cvv', 'otp', 'two_factor_code',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        try {
            ApiLog::create([
                'user_id' => auth()->id(),
                'method' => $request->method(),
                'endpoint' => mb_substr($request->path(), 0, 2048),
                'request_payload' => $this->redact($request->except(['password', 'password_confirmation'])),
                'response_status' => $response->getStatusCode(),
                'response_time_ms' => (int) round((microtime(true) - $start) * 1000),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            // Logging must never break the actual API response.
            report($e);
        }

        return $response;
    }

    protected function redact(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->redact($value);
                continue;
            }

            if (in_array(mb_strtolower((string) $key), $this->redactedFields, true)) {
                $payload[$key] = '***redacted***';
            }
        }

        return $payload;
    }
}
