<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reports)
    {
    }

    public function outstanding(Request $request): JsonResponse
    {
        $this->authorizePermission($request, 'reports.view');

        return response()->json($this->reports->outstandingReport()->map(fn ($c) => [
            'consumer_id' => $c->consumer_id,
            'name' => $c->name,
            'mobile' => $c->mobile,
            'outstanding_balance' => (float) $c->outstanding_balance,
        ]));
    }

    public function profitLoss(Request $request): JsonResponse
    {
        $this->authorizePermission($request, 'reports.view');

        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        return response()->json($this->reports->profitLoss($from, $to));
    }

    public function milkSummary(Request $request): JsonResponse
    {
        $this->authorizePermission($request, 'reports.view');

        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        return response()->json($this->reports->milkSummary($from, $to)->map(fn ($r) => [
            'date' => $r->entry_date->toDateString(),
            'shift' => $r->shift,
            'total_qty' => (float) $r->total_qty,
            'total_amount' => (float) $r->total_amount,
            'customers' => $r->customers,
        ]));
    }

    protected function authorizePermission(Request $request, string $permission): void
    {
        abort_unless($request->user()->can($permission), 403, "Missing permission: {$permission}");
    }
}
