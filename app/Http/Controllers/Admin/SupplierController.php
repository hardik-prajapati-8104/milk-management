<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Models\ActivityLog;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Supplier::class, 'supplier');
    }

    public function index(): View
    {
        return view('admin.suppliers.index', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Suppliers' => null],
        ]);
    }

    public function create(): View
    {
        return view('admin.suppliers.create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $supplier = Supplier::create($request->validated() + [
            'supplier_code' => $this->generateCode(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::record('created', $supplier, new: $supplier->toArray(), description: "Supplier {$supplier->name} created");

        return redirect()->route('admin.suppliers.index')->with('success', "Supplier \"{$supplier->name}\" created.");
    }

    public function show(Supplier $supplier): View
    {
        $movements = $supplier->movements()->with('product')->latest('movement_date')->paginate(30);

        return view('admin.suppliers.show', [
            'supplier' => $supplier,
            'movements' => $movements,
            'breadcrumbs' => [
                'Dashboard' => route('admin.dashboard'),
                'Suppliers' => route('admin.suppliers.index'),
                $supplier->name => null,
            ],
        ]);
    }

    public function edit(Supplier $supplier): View
    {
        return view('admin.suppliers.edit', ['supplier' => $supplier]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $old = $supplier->toArray();
        $supplier->update($request->validated() + ['is_active' => $request->boolean('is_active', true)]);

        ActivityLog::record('updated', $supplier, old: $old, new: $supplier->toArray(), description: "Supplier {$supplier->name} updated");

        return redirect()->route('admin.suppliers.index')->with('success', "Supplier \"{$supplier->name}\" updated.");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->movements()->exists()) {
            return back()->with('error', "Can't delete \"{$supplier->name}\" — it has purchase/collection history.");
        }

        $name = $supplier->name;
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('success', "Supplier \"{$name}\" deleted.");
    }

    protected function generateCode(): string
    {
        $last = Supplier::orderByDesc('id')->first();
        $next = $last ? ((int) preg_replace('/\D/', '', $last->supplier_code)) + 1 : 1;

        return 'SUP' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
