<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorizePermission($request, 'customers.view');

        $query = Customer::with(['village', 'area', 'route'])->search($request->get('q'));

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        return CustomerResource::collection($query->orderBy('name')->paginate($request->integer('per_page', 25)));
    }

    public function show(Request $request, Customer $customer): CustomerResource
    {
        $this->authorizePermission($request, 'customers.view');

        $customer->load(['village', 'area', 'route']);

        return new CustomerResource($customer);
    }

    protected function authorizePermission(Request $request, string $permission): void
    {
        abort_unless($request->user()->can($permission), 403, "Missing permission: {$permission}");
    }
}
