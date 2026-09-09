<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bill\GenerateBillsRequest;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use App\Services\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BillController extends Controller
{
    public function __construct(protected BillingService $billing)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorizePermission($request, 'bills.view');

        $query = Bill::with('customer')
            ->when($request->get('customer_id'), fn ($q, $id) => $q->where('customer_id', $id))
            ->when($request->get('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->get('month'), fn ($q, $m) => $q->where('bill_month', $m))
            ->when($request->get('year'), fn ($q, $y) => $q->where('bill_year', $y));

        return BillResource::collection($query->latest('id')->paginate($request->integer('per_page', 25)));
    }

    public function show(Request $request, Bill $bill): BillResource
    {
        $this->authorizePermission($request, 'bills.view');

        $bill->load(['customer', 'items']);

        return new BillResource($bill);
    }

    public function generate(GenerateBillsRequest $request): JsonResponse
    {
        $result = $this->billing->generateForMonth(
            (int) $request->validated('month'),
            (int) $request->validated('year'),
            $request->validated('customer_ids') ?: null,
            $request->only(['gst_percent', 'delivery_charges', 'due_date'])
        );

        return response()->json([
            'generated' => $result['generated'],
            'skipped_existing' => $result['skippedExisting'],
            'skipped_empty' => $result['skippedEmpty'],
            'bills' => BillResource::collection($result['bills']),
        ]);
    }

    protected function authorizePermission(Request $request, string $permission): void
    {
        abort_unless($request->user()->can($permission), 403, "Missing permission: {$permission}");
    }
}
