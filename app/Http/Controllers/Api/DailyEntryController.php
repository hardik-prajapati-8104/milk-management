<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyEntry\BulkSaveDailyEntryRequest;
use App\Http\Resources\DailyEntryResource;
use App\Models\DailyEntry;
use App\Services\DailyEntryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DailyEntryController extends Controller
{
    public function __construct(protected DailyEntryService $service)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorizePermission($request, 'daily-entries.view');

        $query = DailyEntry::with('customer')
            ->when($request->get('date'), fn ($q, $date) => $q->where('entry_date', $date))
            ->when($request->get('shift'), fn ($q, $shift) => $q->where('shift', $shift))
            ->when($request->get('customer_id'), fn ($q, $id) => $q->where('customer_id', $id));

        return DailyEntryResource::collection($query->latest('entry_date')->paginate($request->integer('per_page', 50)));
    }

    /**
     * Submit a batch of entries from the field (e.g. a delivery-boy mobile app
     * syncing the day's deliveries). Reuses the same service as the web grid,
     * so rate resolution, locking, and amount calculation stay identical.
     */
    public function bulkSave(BulkSaveDailyEntryRequest $request): JsonResponse
    {
        $result = $this->service->bulkSave(
            $request->validated('entry_date'),
            $request->validated('shift'),
            $request->validated('entries')
        );

        return response()->json([
            'message' => "{$result['created']} created, {$result['updated']} updated.",
            'blocked' => $result['blocked'],
        ]);
    }

    protected function authorizePermission(Request $request, string $permission): void
    {
        abort_unless($request->user()->can($permission), 403, "Missing permission: {$permission}");
    }
}
