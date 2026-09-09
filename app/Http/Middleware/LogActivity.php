<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Records a lightweight audit trail entry for state-changing requests.
     * Model-level create/update/delete detail is logged separately by observers;
     * this middleware exists mainly to capture route-level access for security review.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check() && ! $request->isMethod('GET') && $response->getStatusCode() < 400) {
            ActivityLog::record(
                action: strtolower($request->method()),
                description: $request->path()
            );
        }

        return $response;
    }
}
