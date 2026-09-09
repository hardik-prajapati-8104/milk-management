<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExpensesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Excel as ExcelFacade;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Expense::class, 'expense');
    }

    public function index(Request $request): View
    {
        if ($request->ajax()) {
            abort(400, 'Use /admin/expenses/data for AJAX.');
        }

        return view('admin.expenses.index', [
            'categories' => ExpenseCategory::where('is_active', true)->orderBy('name')->get(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Expenses' => null],
        ]);
    }

    public function data(Request $request)
    {
        $query = Expense::with(['category', 'paymentMethod']);

        if ($categoryId = $request->get('category_id')) {
            $query->where('expense_category_id', $categoryId);
        }
        if ($from = $request->get('from')) {
            $query->whereDate('expense_date', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->whereDate('expense_date', '<=', $to);
        }

        return DataTables::eloquent($query->latest('expense_date'))
            ->addColumn('category_name', fn (Expense $e) => $e->category->name ?? '—')
            ->addColumn('method_name', fn (Expense $e) => $e->paymentMethod->name ?? '—')
            ->addColumn('actions', fn (Expense $e) => view('admin.expenses._actions', ['expense' => $e])->render())
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.expenses.create', $this->formData());
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('attachment');

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('expenses/attachments', 'public');
        }
        $data['created_by'] = auth()->id();

        $expense = Expense::create($data);

        ActivityLog::record('created', $expense, new: $expense->toArray(),
            description: "Expense {$expense->expense_number} recorded");

        return redirect()->route('admin.expenses.index')->with('success', "Expense {$expense->expense_number} recorded.");
    }

    public function edit(Expense $expense): View
    {
        return view('admin.expenses.edit', $this->formData() + ['expense' => $expense]);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $old = $expense->toArray();
        $data = $request->safe()->except('attachment');

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('expenses/attachments', 'public');
        }

        $expense->update($data);

        ActivityLog::record('updated', $expense, old: $old, new: $expense->toArray(),
            description: "Expense {$expense->expense_number} updated");

        return redirect()->route('admin.expenses.index')->with('success', "Expense {$expense->expense_number} updated.");
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expenseNumber = $expense->expense_number;
        $expense->delete();

        ActivityLog::record('deleted', description: "Expense {$expenseNumber} deleted");

        return redirect()->route('admin.expenses.index')->with('success', "Expense {$expenseNumber} deleted.");
    }

    public function export(Request $request)
    {
        $this->authorize('export', Expense::class);

        return ExcelFacade::download(
            new ExpensesExport($request->only(['category_id', 'from', 'to'])),
            'expenses-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    protected function formData(): array
    {
        return [
            'categories' => ExpenseCategory::where('is_active', true)->orderBy('name')->get(),
            'methods' => PaymentMethod::where('is_active', true)->get(),
        ];
    }
}
