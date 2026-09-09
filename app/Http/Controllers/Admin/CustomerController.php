<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CustomersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Imports\CustomersImport;
use App\Models\Area;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\Route as DeliveryRoute;
use App\Models\Village;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Excel as ExcelFacade;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerRepositoryInterface $customers,
        protected CustomerService $service,
    ) {
        $this->authorizeResource(Customer::class, 'customer');
    }

    public function index(Request $request): View
    {
        if ($request->ajax()) {
            abort(400, 'Use the /admin/customers/data endpoint for AJAX.');
        }

        return view('admin.customers.index', [
            'areas' => Area::active()->orderBy('name')->get(),
            'routes' => DeliveryRoute::active()->orderBy('name')->get(),
            'categories' => CustomerCategory::where('is_active', true)->orderBy('name')->get(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Customers' => null],
        ]);
    }

    /**
     * Yajra DataTables server-side AJAX source.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->customers->query();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($routeId = $request->get('route_id')) {
            $query->where('route_id', $routeId);
        }

        return DataTables::eloquent($query)
            ->addColumn('village_name', fn (Customer $c) => $c->village->name ?? '—')
            ->addColumn('route_name', fn (Customer $c) => $c->route->name ?? '—')
            ->addColumn('status_badge', function (Customer $c) {
                $map = ['active' => 'success', 'inactive' => 'secondary', 'suspended' => 'danger'];
                return '<span class="badge text-bg-' . $map[$c->status] . '">' . ucfirst($c->status) . '</span>';
            })
            ->addColumn('actions', function (Customer $c) {
                return view('admin.customers._actions', ['customer' => $c])->render();
            })
            ->rawColumns(['status_badge', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.customers.create', $this->formData());
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = $this->service->createCustomer(
            $request->safe()->except(['photo', 'documents']),
            $request->file('photo'),
            $request->file('documents', [])
        );

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', "Customer {$customer->consumer_id} created successfully.");
    }

    public function show(Customer $customer): View
    {
        $customer->load(['village', 'area', 'route', 'category', 'documents', 'dailyEntries' => fn ($q) => $q->latest()->limit(10)]);

        return view('admin.customers.show', [
            'customer' => $customer,
            'breadcrumbs' => [
                'Dashboard' => route('admin.dashboard'),
                'Customers' => route('admin.customers.index'),
                $customer->consumer_id => null,
            ],
        ]);
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.edit', $this->formData() + ['customer' => $customer]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->service->updateCustomer(
            $customer,
            $request->safe()->except(['photo', 'documents']),
            $request->file('photo'),
            $request->file('documents', [])
        );

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', "Customer {$customer->consumer_id} updated successfully.");
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $consumerId = $customer->consumer_id;
        $this->service->deleteCustomer($customer);

        return redirect()->route('admin.customers.index')
            ->with('success', "Customer {$consumerId} deleted.");
    }

    public function export(Request $request)
    {
        $this->authorize('export', Customer::class);

        return ExcelFacade::download(
            new CustomersExport($request->only(['status', 'route_id', 'search'])),
            'customers-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function importForm(): View
    {
        $this->authorize('create', Customer::class);

        return view('admin.customers.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $this->authorize('create', Customer::class);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $import = new CustomersImport();
        ExcelFacade::import($import, $request->file('file'));

        $message = "{$import->imported} customers imported successfully.";
        if ($errors = $import->getErrors()) {
            return back()->with('error', $message . ' Errors: ' . implode(' | ', array_slice($errors, 0, 5)));
        }

        return redirect()->route('admin.customers.index')->with('success', $message);
    }

    protected function formData(): array
    {
        return [
            'villages' => Village::orderBy('name')->get(),
            'areas' => Area::active()->orderBy('name')->get(),
            'routes' => DeliveryRoute::active()->orderBy('name')->get(),
            'categories' => CustomerCategory::where('is_active', true)->orderBy('name')->get(),
        ];
    }
}
