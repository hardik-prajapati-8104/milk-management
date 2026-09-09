<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreInventoryMovementRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(protected InventoryService $inventory)
    {
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::orderBy('name')->get(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Products & Inventory' => null],
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated() + ['is_active' => $request->boolean('is_active', true)]);

        ActivityLog::record('created', $product, new: $product->toArray(), description: "Product {$product->name} created");

        return redirect()->route('admin.products.index')->with('success', "Product \"{$product->name}\" created.");
    }

    public function show(Product $product): View
    {
        $movements = $product->movements()->with(['supplier', 'createdBy'])->latest('movement_date')->paginate(30);

        return view('admin.products.show', [
            'product' => $product,
            'movements' => $movements,
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'breadcrumbs' => [
                'Dashboard' => route('admin.dashboard'),
                'Products & Inventory' => route('admin.products.index'),
                $product->name => null,
            ],
        ]);
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', ['product' => $product]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $old = $product->toArray();
        $product->update($request->validated() + ['is_active' => $request->boolean('is_active', true)]);

        ActivityLog::record('updated', $product, old: $old, new: $product->toArray(), description: "Product {$product->name} updated");

        return redirect()->route('admin.products.index')->with('success', "Product \"{$product->name}\" updated.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->movements()->exists()) {
            return back()->with('error', "Can't delete \"{$product->name}\" — it has stock movement history.");
        }

        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', "Product \"{$name}\" deleted.");
    }

    /**
     * Record a stock movement (collection, purchase, sale, wastage, transfer, adjustment)
     * against a product from its detail page.
     */
    public function recordMovement(StoreInventoryMovementRequest $request, Product $product): RedirectResponse
    {
        try {
            $this->inventory->recordMovement($request->validated() + ['product_id' => $product->id]);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.products.show', $product)->with('success', 'Stock movement recorded.');
    }
}
