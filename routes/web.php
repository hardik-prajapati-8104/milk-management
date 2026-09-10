<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\BillController;
use App\Http\Controllers\Admin\BirthdayController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DailyEntryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\CashSaleController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\MilkPurchaseController;
use App\Http\Controllers\Admin\MilkRateController;
use App\Http\Controllers\Admin\MilkStockController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

// Route::redirect('/', '/login');

Route::get('/', function () {
    return 'Claude Project is working!';
});
 

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
| Every route below sits behind ['auth', 'verified'] and, per-module,
| a Spatie 'permission:' check matching the RolePermissionSeeder matrix.
| Controllers for modules beyond Phase 1 (Customers, Daily Entries, Bills,
| Payments, Expenses, Products, Employees, Routes, Reports, Users, Settings)
| are added module-by-module in subsequent phases; routes are declared here
| now so the layout's sidebar links resolve without errors.
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('search', [DashboardController::class, 'index'])->name('search'); // replaced by GlobalSearchController later

    Route::middleware('permission:customers.view')->prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('data', [CustomerController::class, 'data'])->name('data');
        Route::get('export', [CustomerController::class, 'export'])->name('export');
        Route::get('import', [CustomerController::class, 'importForm'])->name('import-form');
        Route::post('import', [CustomerController::class, 'import'])->name('import');
        Route::get('create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('permission:daily-entries.view')->prefix('daily-entries')->name('daily-entries.')->group(function () {
        Route::get('/', [DailyEntryController::class, 'index'])->name('index');
        Route::get('search', [DailyEntryController::class, 'search'])->name('search');
        Route::post('bulk-save', [DailyEntryController::class, 'bulkSave'])->name('bulk-save');
        Route::post('copy-previous', [DailyEntryController::class, 'copyPreviousDay'])->name('copy-previous');
        Route::get('calendar', [DailyEntryController::class, 'calendar'])->name('calendar');
        Route::post('{dailyEntry}/toggle-lock', [DailyEntryController::class, 'toggleLock'])->name('toggle-lock');
    });

    /*
    |----------------------------------------------------------------------
    | Calendar
    |----------------------------------------------------------------------
    | One page (FullCalendar) shows Events, Meetings, Reminders and Tasks
    | (all backed by CalendarEvent) plus read-only Holiday and Birthday
    | overlays. Holidays and Birthdays each additionally get their own
    | DataTables CRUD screen for bulk management.
    */
    Route::middleware('permission:calendar.view')->prefix('calendar')->name('calendar.')->group(function () {
        Route::get('/', [CalendarController::class, 'index'])->name('index');
        Route::get('feed', [CalendarController::class, 'feed'])->name('feed');
        Route::get('{calendar_event}', [CalendarController::class, 'show'])->name('show');
        Route::post('/', [CalendarController::class, 'store'])->name('store');
        Route::put('{calendar_event}', [CalendarController::class, 'update'])->name('update');
        Route::post('{calendar_event}/reschedule', [CalendarController::class, 'reschedule'])->name('reschedule');
        Route::post('{calendar_event}/status', [CalendarController::class, 'updateStatus'])->name('update-status');
        Route::delete('{calendar_event}', [CalendarController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('permission:holidays.view')->prefix('holidays')->name('holidays.')->group(function () {
        Route::get('/', [HolidayController::class, 'index'])->name('index');
        Route::get('data', [HolidayController::class, 'data'])->name('data');
        Route::get('create', [HolidayController::class, 'create'])->name('create');
        Route::post('/', [HolidayController::class, 'store'])->name('store');
        Route::get('{holiday}/edit', [HolidayController::class, 'edit'])->name('edit');
        Route::put('{holiday}', [HolidayController::class, 'update'])->name('update');
        Route::delete('{holiday}', [HolidayController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('permission:birthdays.view')->prefix('birthdays')->name('birthdays.')->group(function () {
        Route::get('/', [BirthdayController::class, 'index'])->name('index');
        Route::get('data', [BirthdayController::class, 'data'])->name('data');
        Route::get('create', [BirthdayController::class, 'create'])->name('create');
        Route::post('/', [BirthdayController::class, 'store'])->name('store');
        Route::get('{birthday}/edit', [BirthdayController::class, 'edit'])->name('edit');
        Route::put('{birthday}', [BirthdayController::class, 'update'])->name('update');
        Route::delete('{birthday}', [BirthdayController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('permission:milk-rates.view')->prefix('milk-rates')->name('milk-rates.')->group(function () {
        Route::get('/', [MilkRateController::class, 'index'])->name('index');
        Route::get('create', [MilkRateController::class, 'create'])->name('create');
        Route::post('/', [MilkRateController::class, 'store'])->name('store');
        Route::get('{milk_rate}/edit', [MilkRateController::class, 'edit'])->name('edit');
        Route::put('{milk_rate}', [MilkRateController::class, 'update'])->name('update');
        Route::delete('{milk_rate}', [MilkRateController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('permission:milk-purchases.view')->prefix('milk-purchases')->name('milk-purchases.')->group(function () {
        Route::get('/', [MilkPurchaseController::class, 'index'])->name('index');
        Route::get('data', [MilkPurchaseController::class, 'data'])->name('data');
        Route::get('create', [MilkPurchaseController::class, 'create'])->name('create');
        Route::post('/', [MilkPurchaseController::class, 'store'])->name('store');
        Route::get('{milk_purchase}/edit', [MilkPurchaseController::class, 'edit'])->name('edit');
        Route::put('{milk_purchase}', [MilkPurchaseController::class, 'update'])->name('update');
        Route::delete('{milk_purchase}', [MilkPurchaseController::class, 'destroy'])->name('destroy');
        Route::post('{milk_purchase}/payment', [MilkPurchaseController::class, 'recordPayment'])->name('record-payment');
    });

    Route::middleware('permission:milk-stock.view')->prefix('milk-stock')->name('milk-stock.')->group(function () {
        Route::get('/', [MilkStockController::class, 'index'])->name('index');
        Route::get('data', [MilkStockController::class, 'data'])->name('data');
    });

    Route::middleware('permission:cash-sales.view')->prefix('cash-sales')->name('cash-sales.')->group(function () {
        Route::get('/', [CashSaleController::class, 'index'])->name('index');
        Route::get('data', [CashSaleController::class, 'data'])->name('data');
        Route::get('create', [CashSaleController::class, 'create'])->name('create');
        Route::post('/', [CashSaleController::class, 'store'])->name('store');
        Route::get('{cash_sale}/edit', [CashSaleController::class, 'edit'])->name('edit');
        Route::put('{cash_sale}', [CashSaleController::class, 'update'])->name('update');
        Route::delete('{cash_sale}', [CashSaleController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('permission:bills.view')->prefix('bills')->name('bills.')->group(function () {
        Route::get('/', [BillController::class, 'index'])->name('index');
        Route::get('data', [BillController::class, 'data'])->name('data');
        Route::get('generate', [BillController::class, 'generateForm'])->name('generate-form');
        Route::post('generate', [BillController::class, 'generate'])->name('generate');
        Route::get('{bill}', [BillController::class, 'show'])->name('show');
        Route::get('{bill}/edit', [BillController::class, 'edit'])->name('edit');
        Route::put('{bill}', [BillController::class, 'update'])->name('update');
        Route::delete('{bill}', [BillController::class, 'destroy'])->name('destroy');
        Route::get('{bill}/pdf', [BillController::class, 'downloadPdf'])->name('pdf');
        Route::get('{bill}/whatsapp', [BillController::class, 'whatsappLink'])->name('whatsapp');
    });

    Route::middleware('permission:payments.view')->prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('data', [PaymentController::class, 'data'])->name('data');
        Route::get('create', [PaymentController::class, 'create'])->name('create');
        Route::post('/', [PaymentController::class, 'store'])->name('store');
        Route::get('customer/{customer}/outstanding', [PaymentController::class, 'customerOutstanding'])->name('customer-outstanding');
        Route::get('{payment}', [PaymentController::class, 'show'])->name('show');
        Route::delete('{payment}', [PaymentController::class, 'destroy'])->name('destroy');
        Route::get('{payment}/pdf', [PaymentController::class, 'downloadPdf'])->name('pdf');
    });

    Route::middleware('permission:expenses.view')->prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('data', [ExpenseController::class, 'data'])->name('data');
        Route::get('export', [ExpenseController::class, 'export'])->name('export');
        Route::get('create', [ExpenseController::class, 'create'])->name('create');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
        Route::get('{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
        Route::put('{expense}', [ExpenseController::class, 'update'])->name('update');
        Route::delete('{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('permission:products.view')->prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('{product}', [ProductController::class, 'show'])->name('show');
        Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::post('{product}/movements', [ProductController::class, 'recordMovement'])->name('record-movement');
    });

    Route::middleware('permission:suppliers.view')->prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('create', [SupplierController::class, 'create'])->name('create');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('{supplier}', [SupplierController::class, 'show'])->name('show');
        Route::get('{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('permission:employees.view')->prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('data', [EmployeeController::class, 'data'])->name('data');
        Route::get('attendance', [EmployeeController::class, 'attendanceForm'])->name('attendance-form');
        Route::post('attendance', [EmployeeController::class, 'markAttendance'])->name('mark-attendance');
        Route::get('create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('permission:routes.view')->prefix('routes-areas')->name('routes.')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');

        Route::post('areas', [LocationController::class, 'storeArea'])->name('areas.store');
        Route::put('areas/{area}', [LocationController::class, 'updateArea'])->name('areas.update');
        Route::delete('areas/{area}', [LocationController::class, 'destroyArea'])->name('areas.destroy');

        Route::post('villages', [LocationController::class, 'storeVillage'])->name('villages.store');
        Route::put('villages/{village}', [LocationController::class, 'updateVillage'])->name('villages.update');
        Route::delete('villages/{village}', [LocationController::class, 'destroyVillage'])->name('villages.destroy');

        Route::post('delivery-routes', [LocationController::class, 'storeRoute'])->name('delivery-routes.store');
        Route::put('delivery-routes/{route}', [LocationController::class, 'updateRoute'])->name('delivery-routes.update');
        Route::delete('delivery-routes/{route}', [LocationController::class, 'destroyRoute'])->name('delivery-routes.destroy');
    });

    Route::middleware('permission:reports.view')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('milk-summary', [ReportController::class, 'milkSummary'])->name('milk-summary');
        Route::get('collection', [ReportController::class, 'collection'])->name('collection');
        Route::get('outstanding', [ReportController::class, 'outstanding'])->name('outstanding');
        Route::get('customer-ledger', [ReportController::class, 'customerLedger'])->name('customer-ledger');
        Route::get('area', [ReportController::class, 'area'])->name('area');
        Route::get('village', [ReportController::class, 'village'])->name('village');
        Route::get('route', [ReportController::class, 'route'])->name('route');
        Route::get('expense', [ReportController::class, 'expense'])->name('expense');
        Route::get('profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('monthly-summary', [ReportController::class, 'monthlySummary'])->name('monthly-summary');
        Route::get('export/{type}', [ReportController::class, 'export'])->name('export');
    });

    Route::middleware('permission:users.view')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('{user}', [UserController::class, 'update'])->name('update');
        Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('permission:roles.view')->prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('permission:settings.view')->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('{group}', [SettingController::class, 'update'])->name('update');
    });

    /*
    |----------------------------------------------------------------------
    | Activity & Audit
    |----------------------------------------------------------------------
    | Covers Activity Logs, Login Logs, Error Logs, API Logs, Audit Trail,
    | User Timeline, Admin Actions, IP Tracking, Browser & Device Info.
    | Every route sits behind 'activity-logs.view' (enforced again in the
    | controller constructor); export/purge additionally check the
    | ActivityLogPolicy's export/manage abilities.
    */
    Route::middleware('permission:activity-logs.view')->prefix('audit')->name('audit.')->group(function () {
        Route::get('/', [AuditController::class, 'index'])->name('index');

        Route::get('data', [AuditController::class, 'activityData'])->name('data');
        Route::get('audit-trail-data', [AuditController::class, 'auditTrailData'])->name('audit-trail-data');
        Route::get('login-data', [AuditController::class, 'loginData'])->name('login-data');
        Route::get('error-data', [AuditController::class, 'errorData'])->name('error-data');
        Route::get('api-data', [AuditController::class, 'apiData'])->name('api-data');
        Route::get('admin-actions-data', [AuditController::class, 'adminActionsData'])->name('admin-actions-data');
        Route::get('ip-data', [AuditController::class, 'ipData'])->name('ip-data');
        Route::get('browser-stats', [AuditController::class, 'browserStats'])->name('browser-stats');
        Route::get('device-stats', [AuditController::class, 'deviceStats'])->name('device-stats');

        Route::get('timeline/{user}', [AuditController::class, 'userTimeline'])->name('timeline');
        Route::get('timeline-data', [AuditController::class, 'timelineData'])->name('timeline-data');

        Route::get('entry/{log}', [AuditController::class, 'show'])->name('show');
        Route::get('api-entry/{apiLog}', [AuditController::class, 'apiShow'])->name('api-show');
        Route::get('error-entry/{error}', [AuditController::class, 'errorShow'])->name('error-show');
        Route::post('error/{error}/resolve', [AuditController::class, 'resolveError'])->name('error-resolve');

        Route::post('purge', [AuditController::class, 'purge'])->name('purge');
        Route::get('export/{type}', [AuditController::class, 'export'])->name('export');
    });
    
});
