<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorizePermission($request, 'payments.view');

        $query = Payment::with(['customer', 'paymentMethod'])
            ->when($request->get('customer_id'), fn ($q, $id) => $q->where('customer_id', $id))
            ->when($request->get('from'), fn ($q, $from) => $q->whereDate('payment_date', '>=', $from))
            ->when($request->get('to'), fn ($q, $to) => $q->whereDate('payment_date', '<=', $to));

        return PaymentResource::collection($query->latest('id')->paginate($request->integer('per_page', 25)));
    }

    public function show(Request $request, Payment $payment): PaymentResource
    {
        $this->authorizePermission($request, 'payments.view');

        $payment->load(['customer', 'bill', 'paymentMethod']);

        return new PaymentResource($payment);
    }

    public function store(StorePaymentRequest $request): PaymentResource
    {
        $payment = $this->service->record($request->validated());

        return new PaymentResource($payment->load(['customer', 'bill', 'paymentMethod']));
    }

    protected function authorizePermission(Request $request, string $permission): void
    {
        abort_unless($request->user()->can($permission), 403, "Missing permission: {$permission}");
    }
}
