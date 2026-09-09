<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\EmployeeDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Employee::class, 'employee');
    }

    public function index(Request $request): View
    {
        if ($request->ajax()) {
            abort(400, 'Use /admin/employees/data for AJAX.');
        }

        return view('admin.employees.index', [
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Employees' => null],
        ]);
    }

    public function data()
    {
        return DataTables::eloquent(Employee::query())
            ->addColumn('status_badge', function (Employee $e) {
                $map = ['active' => 'success', 'inactive' => 'secondary', 'terminated' => 'danger'];
                return '<span class="badge text-bg-' . $map[$e->status] . '">' . ucfirst($e->status) . '</span>';
            })
            ->addColumn('actions', fn (Employee $e) => view('admin.employees._actions', ['employee' => $e])->render())
            ->rawColumns(['status_badge', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.employees.create');
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['photo', 'documents']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employees/photos', 'public');
        }

        $data['employee_id'] = $this->generateEmployeeId();

        $employee = Employee::create($data);
        $this->attachDocuments($employee, $request->file('documents', []));

        ActivityLog::record('created', $employee, new: $employee->toArray(),
            description: "Employee {$employee->employee_id} created");

        return redirect()->route('admin.employees.index')->with('success', "Employee {$employee->employee_id} created.");
    }

    public function show(Employee $employee): View
    {
        $employee->load(['documents', 'attendances' => fn ($q) => $q->latest('attendance_date')->limit(31)]);

        return view('admin.employees.show', [
            'employee' => $employee,
            'breadcrumbs' => [
                'Dashboard' => route('admin.dashboard'),
                'Employees' => route('admin.employees.index'),
                $employee->employee_id => null,
            ],
        ]);
    }

    public function edit(Employee $employee): View
    {
        return view('admin.employees.edit', ['employee' => $employee]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $old = $employee->toArray();
        $data = $request->safe()->except(['photo', 'documents']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employees/photos', 'public');
        }

        $employee->update($data);
        $this->attachDocuments($employee, $request->file('documents', []));

        ActivityLog::record('updated', $employee, old: $old, new: $employee->toArray(),
            description: "Employee {$employee->employee_id} updated");

        return redirect()->route('admin.employees.index')->with('success', "Employee {$employee->employee_id} updated.");
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employeeId = $employee->employee_id;
        $employee->delete();

        ActivityLog::record('deleted', description: "Employee {$employeeId} deleted");

        return redirect()->route('admin.employees.index')->with('success', "Employee {$employeeId} deleted.");
    }

    /**
     * Mark today's (or a given date's) attendance for every employee in one request.
     */
    public function markAttendance(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('employees.edit'), 403);

        $request->validate([
            'attendance_date' => ['required', 'date'],
            'records' => ['required', 'array'],
            'records.*.employee_id' => ['required', 'exists:employees,id'],
            'records.*.status' => ['required', 'in:present,absent,half_day,leave,holiday'],
        ]);

        foreach ($request->records as $record) {
            EmployeeAttendance::updateOrCreate(
                ['employee_id' => $record['employee_id'], 'attendance_date' => $request->attendance_date],
                ['status' => $record['status'], 'remarks' => $record['remarks'] ?? null]
            );
        }

        return response()->json(['message' => 'Attendance saved for ' . $request->attendance_date . '.']);
    }

    public function attendanceForm(Request $request): View
    {
        abort_unless($request->user()->can('employees.view'), 403);

        $date = $request->get('date', now()->toDateString());
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $existing = EmployeeAttendance::where('attendance_date', $date)->get()->keyBy('employee_id');

        return view('admin.employees.attendance', [
            'date' => $date,
            'employees' => $employees,
            'existing' => $existing,
            'breadcrumbs' => [
                'Dashboard' => route('admin.dashboard'),
                'Employees' => route('admin.employees.index'),
                'Attendance' => null,
            ],
        ]);
    }

    protected function attachDocuments(Employee $employee, array $documents): void
    {
        foreach ($documents as $document) {
            EmployeeDocument::create([
                'employee_id' => $employee->id,
                'title' => $document->getClientOriginalName(),
                'file_path' => $document->store('employees/documents', 'public'),
            ]);
        }
    }

    protected function generateEmployeeId(): string
    {
        $last = Employee::orderByDesc('id')->first();
        $next = $last ? ((int) preg_replace('/\D/', '', $last->employee_id)) + 1 : 1;

        return 'EMP' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
