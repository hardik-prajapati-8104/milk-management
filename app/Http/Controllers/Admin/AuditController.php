<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GenericTableExport;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ApiLog;
use App\Models\ErrorLog;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Excel as ExcelFacade;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:activity-logs.view');
    }

    /**
     * Single tabbed page for every Activity & Audit sub-feature. Each tab
     * pulls its own data lazily via its "data" endpoint below, so opening
     * the page stays fast even once the logs tables grow large.
     */
    public function index(): View
    {
        $today = now()->toDateString();

        $stats = [
            'activity_total' => ActivityLog::ofType('activity')->count(),
            'audit_total' => ActivityLog::ofType('audit')->count(),
            'logins_today' => LoginLog::whereDate('created_at', $today)->status('success')->count(),
            'failed_logins_today' => LoginLog::whereDate('created_at', $today)->whereIn('status', ['failed', 'locked_out'])->count(),
            'unresolved_errors' => ErrorLog::unresolved()->count(),
            'api_calls_today' => ApiLog::whereDate('created_at', $today)->count(),
            'distinct_ips_today' => ActivityLog::whereDate('created_at', $today)->whereNotNull('ip_address')->distinct('ip_address')->count('ip_address'),
        ];

        return view('admin.audit.index', [
            'stats' => $stats,
            'users' => User::orderBy('name')->get(['id', 'name', 'email']),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Activity & Audit' => null],
            'canExport' => auth()->user()->can('activity-logs.export'),
            'canManage' => auth()->user()->can('activity-logs.manage'),
        ]);
    }

    /* -----------------------------------------------------------------
     | 1. Activity Logs
     |------------------------------------------------------------------ */
    public function activityData(Request $request): JsonResponse
    {
        return $this->activityLikeData($request, 'activity');
    }

    /* -----------------------------------------------------------------
     | 5. Audit Trail (field-level before/after diffs)
     |------------------------------------------------------------------ */
    public function auditTrailData(Request $request): JsonResponse
    {
        return $this->activityLikeData($request, 'audit');
    }

    /**
     * Shared DataTables query for both Activity Logs and Audit Trail tabs;
     * they're the same underlying table, filtered by log_type.
     */
    protected function activityLikeData(Request $request, string $logType): JsonResponse
    {
        $query = ActivityLog::with('user')->ofType($logType);

        $this->applyCommonFilters($query, $request);

        return DataTables::eloquent($query)
            ->addColumn('user_name', fn (ActivityLog $log) => $log->user->name ?? 'System')
            ->addColumn('subject', fn (ActivityLog $log) => $log->subject_type ? class_basename($log->subject_type) . ' #' . $log->subject_id : '—')
            ->addColumn('device', fn (ActivityLog $log) => trim(($log->browser ?? '—') . ' / ' . ($log->platform ?? '—')))
            ->addColumn('action_badge', function (ActivityLog $log) {
                $map = ['created' => 'success', 'updated' => 'primary', 'deleted' => 'danger', 'login' => 'info', 'logout' => 'secondary'];
                $color = $map[$log->action] ?? 'secondary';

                return '<span class="badge text-bg-' . $color . '">' . e(ucfirst(str_replace('-', ' ', $log->action))) . '</span>';
            })
            ->addColumn('actions', fn (ActivityLog $log) => view('admin.audit._log-actions', ['log' => $log])->render())
            ->rawColumns(['action_badge', 'actions'])
            ->orderColumn('created_at', 'created_at $1')
            ->toJson();
    }

    /* -----------------------------------------------------------------
     | 2. Login Logs
     |------------------------------------------------------------------ */
    public function loginData(Request $request): JsonResponse
    {
        $query = LoginLog::with('user');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }
        if ($from = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        return DataTables::eloquent($query)
            ->addColumn('user_name', fn (LoginLog $l) => $l->user->name ?? $l->email ?? 'Unknown')
            ->addColumn('device', fn (LoginLog $l) => trim(($l->browser ?? '—') . ' / ' . ($l->platform ?? '—')))
            ->addColumn('status_badge', function (LoginLog $l) {
                $map = ['success' => 'success', 'failed' => 'danger', 'locked_out' => 'warning', 'logged_out' => 'secondary'];
                $color = $map[$l->status] ?? 'secondary';

                return '<span class="badge text-bg-' . $color . '">' . e(str_replace('_', ' ', ucfirst($l->status))) . '</span>';
            })
            ->rawColumns(['status_badge'])
            ->orderColumn('created_at', 'created_at $1')
            ->toJson();
    }

    /* -----------------------------------------------------------------
     | 3. Error Logs
     |------------------------------------------------------------------ */
    public function errorData(Request $request): JsonResponse
    {
        $query = ErrorLog::with('user');

        if ($level = $request->get('level')) {
            $query->where('level', $level);
        }
        if ($request->get('unresolved_only') === '1') {
            $query->unresolved();
        }

        return DataTables::eloquent($query)
            ->addColumn('user_name', fn (ErrorLog $e) => $e->user->name ?? 'Guest')
            ->addColumn('level_badge', function (ErrorLog $e) {
                $map = ['critical' => 'danger', 'error' => 'warning', 'warning' => 'secondary'];
                $color = $map[$e->level] ?? 'secondary';

                return '<span class="badge text-bg-' . $color . '">' . e(ucfirst($e->level)) . '</span>';
            })
            ->addColumn('resolved_badge', fn (ErrorLog $e) => $e->is_resolved
                ? '<span class="badge text-bg-success">Resolved</span>'
                : '<span class="badge text-bg-danger">Open</span>')
            ->addColumn('actions', fn (ErrorLog $e) => view('admin.audit._error-actions', ['error' => $e])->render())
            ->rawColumns(['level_badge', 'resolved_badge', 'actions'])
            ->orderColumn('created_at', 'created_at $1')
            ->toJson();
    }

    public function errorShow(ErrorLog $error): JsonResponse
    {
        $error->load('user:id,name,email');

        return response()->json($error);
    }

    public function resolveError(ErrorLog $error): RedirectResponse
    {
        $this->authorize('manage', ActivityLog::class);

        $error->update(['is_resolved' => true]);

        return back()->with('success', 'Error marked as resolved.');
    }

    /* -----------------------------------------------------------------
     | 4. API Logs
     |------------------------------------------------------------------ */
    public function apiData(Request $request): JsonResponse
    {
        $query = ApiLog::with('user');

        if ($status = $request->get('status')) {
            $status === 'failed' ? $query->failed() : $query->where('response_status', '<', 400);
        }
        if ($method = $request->get('method')) {
            $query->where('method', $method);
        }

        return DataTables::eloquent($query)
            ->addColumn('user_name', fn (ApiLog $a) => $a->user->name ?? 'Guest/Unauthenticated')
            ->addColumn('status_badge', function (ApiLog $a) {
                $color = $a->response_status >= 500 ? 'danger' : ($a->response_status >= 400 ? 'warning' : 'success');

                return '<span class="badge text-bg-' . $color . '">' . (int) $a->response_status . '</span>';
            })
            ->addColumn('actions', fn (ApiLog $a) => '<button type="button" class="btn btn-sm btn-outline-secondary js-view-api" data-id="' . $a->id . '"><i class="bi bi-eye"></i></button>')
            ->rawColumns(['status_badge', 'actions'])
            ->orderColumn('created_at', 'created_at $1')
            ->toJson();
    }

    public function apiShow(ApiLog $apiLog): JsonResponse
    {
        return response()->json($apiLog);
    }

    /* -----------------------------------------------------------------
     | 6. User Timeline
     |------------------------------------------------------------------ */
    public function userTimeline(User $user): View
    {
        return view('admin.audit.timeline', [
            'timelineUser' => $user,
            'breadcrumbs' => [
                'Dashboard' => route('admin.dashboard'),
                'Activity & Audit' => route('admin.audit.index'),
                $user->name => null,
            ],
        ]);
    }

    public function timelineData(Request $request): JsonResponse
    {
        $userId = (int) $request->get('user_id');

        $activity = ActivityLog::where('user_id', $userId)
            ->select('id', 'action', 'description', 'log_type', 'ip_address', 'browser', 'platform', 'device_type', 'subject_type', 'subject_id', 'created_at')
            ->get()
            ->map(fn ($row) => [
                'source' => $row->log_type === 'audit' ? 'Audit Trail' : 'Activity',
                'title' => ucfirst(str_replace('-', ' ', $row->action)) . ($row->subject_type ? ' ' . class_basename($row->subject_type) : ''),
                'description' => $row->description,
                'ip_address' => $row->ip_address,
                'device' => trim(($row->browser ?? '—') . ' / ' . ($row->platform ?? '—')),
                'created_at' => $row->created_at,
            ]);

        $logins = LoginLog::where('user_id', $userId)
            ->select('id', 'status', 'reason', 'ip_address', 'browser', 'platform', 'created_at')
            ->get()
            ->map(fn ($row) => [
                'source' => 'Login',
                'title' => str_replace('_', ' ', ucfirst($row->status)),
                'description' => $row->reason,
                'ip_address' => $row->ip_address,
                'device' => trim(($row->browser ?? '—') . ' / ' . ($row->platform ?? '—')),
                'created_at' => $row->created_at,
            ]);

        $timeline = $activity->concat($logins)
            ->sortByDesc('created_at')
            ->values()
            ->take(300);

        return response()->json(['data' => $timeline]);
    }

    /* -----------------------------------------------------------------
     | 7. Admin Actions
     |------------------------------------------------------------------ */
    public function adminActionsData(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user')
            ->whereHas('user', fn ($q) => $q->role(['Admin', 'Manager']));

        $this->applyCommonFilters($query, $request);

        return DataTables::eloquent($query)
            ->addColumn('user_name', fn (ActivityLog $log) => $log->user->name ?? 'System')
            ->addColumn('role', fn (ActivityLog $log) => $log->user?->getRoleNames()->join(', ') ?? '—')
            ->addColumn('subject', fn (ActivityLog $log) => $log->subject_type ? class_basename($log->subject_type) . ' #' . $log->subject_id : '—')
            ->addColumn('action_badge', fn (ActivityLog $log) => '<span class="badge text-bg-primary">' . e(ucfirst(str_replace('-', ' ', $log->action))) . '</span>')
            ->rawColumns(['action_badge'])
            ->orderColumn('created_at', 'created_at $1')
            ->toJson();
    }

    /* -----------------------------------------------------------------
     | 8. IP Tracking
     |------------------------------------------------------------------ */
    public function ipData(Request $request): JsonResponse
    {
        $query = DB::table('activity_logs')
            ->select(
                'ip_address',
                DB::raw('COUNT(*) as total_events'),
                DB::raw('COUNT(DISTINCT user_id) as distinct_users'),
                DB::raw('MAX(created_at) as last_seen'),
                DB::raw('MIN(created_at) as first_seen')
            )
            ->whereNotNull('ip_address')
            ->groupBy('ip_address');

        return DataTables::query($query)
            ->addColumn('users_list', function ($row) {
                $names = ActivityLog::where('ip_address', $row->ip_address)
                    ->whereNotNull('user_id')->with('user:id,name')
                    ->get()->pluck('user.name')->filter()->unique()->take(5)->implode(', ');

                return $names !== '' ? $names : '—';
            })
            ->addColumn('failed_logins', fn ($row) => LoginLog::where('ip_address', $row->ip_address)->where('status', 'failed')->count())
            ->addColumn('flag', function ($row) {
                if ($row->distinct_users > 2) {
                    return '<span class="badge text-bg-warning" title="Multiple accounts used from this IP">Shared IP</span>';
                }

                return '<span class="badge text-bg-light text-dark">Normal</span>';
            })
            ->rawColumns(['flag'])
            ->toJson();
    }

    /* -----------------------------------------------------------------
     | 9. Browser Information
     |------------------------------------------------------------------ */
    public function browserStats(): JsonResponse
    {
        $breakdown = ActivityLog::select('browser', DB::raw('COUNT(*) as total'))
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderByDesc('total')
            ->get();

        return response()->json(['data' => $breakdown]);
    }

    /* -----------------------------------------------------------------
     | 10. Device Information
     |------------------------------------------------------------------ */
    public function deviceStats(): JsonResponse
    {
        $byDevice = ActivityLog::select('device_type', DB::raw('COUNT(*) as total'))
            ->whereNotNull('device_type')->groupBy('device_type')->orderByDesc('total')->get();

        $byPlatform = ActivityLog::select('platform', DB::raw('COUNT(*) as total'))
            ->whereNotNull('platform')->groupBy('platform')->orderByDesc('total')->get();

        return response()->json(['by_device' => $byDevice, 'by_platform' => $byPlatform]);
    }

    /* -----------------------------------------------------------------
     | Detail view (modal) + maintenance actions
     |------------------------------------------------------------------ */
    public function show(ActivityLog $log): JsonResponse
    {
        $log->load('user:id,name,email');

        return response()->json($log);
    }

    public function purge(Request $request): RedirectResponse
    {
        $this->authorize('manage', ActivityLog::class);

        $data = $request->validate([
            'days' => ['required', 'integer', 'min:30', 'max:3650'],
            'types' => ['required', 'array'],
            'types.*' => ['in:activity,audit,login,error,api'],
        ]);

        $cutoff = now()->subDays($data['days']);
        $purged = 0;

        if (in_array('activity', $data['types'], true)) {
            $purged += ActivityLog::ofType('activity')->where('created_at', '<', $cutoff)->delete();
        }
        if (in_array('audit', $data['types'], true)) {
            $purged += ActivityLog::ofType('audit')->where('created_at', '<', $cutoff)->delete();
        }
        if (in_array('login', $data['types'], true)) {
            $purged += LoginLog::where('created_at', '<', $cutoff)->delete();
        }
        if (in_array('error', $data['types'], true)) {
            $purged += ErrorLog::where('is_resolved', true)->where('created_at', '<', $cutoff)->delete();
        }
        if (in_array('api', $data['types'], true)) {
            $purged += ApiLog::where('created_at', '<', $cutoff)->delete();
        }

        ActivityLog::record('purged', description: "Purged {$purged} log records older than {$data['days']} days.");

        return back()->with('success', "Purged {$purged} log records older than {$data['days']} days.");
    }

    /* -----------------------------------------------------------------
     | Export
     |------------------------------------------------------------------ */
    public function export(Request $request, string $type)
    {
        $this->authorize('export', ActivityLog::class);

        [$headings, $rows, $title] = match ($type) {
            'activity' => $this->exportActivityLike('activity', 'Activity Logs'),
            'audit' => $this->exportActivityLike('audit', 'Audit Trail'),
            'login' => $this->exportLoginLogs(),
            'error' => $this->exportErrorLogs(),
            'api' => $this->exportApiLogs(),
            default => abort(404),
        };

        $export = new GenericTableExport($headings, $rows, $title);

        return ExcelFacade::download($export, $title . '.xlsx');
    }

    protected function exportActivityLike(string $logType, string $title): array
    {
        $rows = ActivityLog::with('user')->ofType($logType)->latest()->limit(5000)->get()
            ->map(fn (ActivityLog $l) => [
                $l->created_at?->format('Y-m-d H:i:s'),
                $l->user->name ?? 'System',
                $l->action,
                $l->subject_type ? class_basename($l->subject_type) . ' #' . $l->subject_id : '',
                $l->description,
                $l->ip_address,
                $l->browser,
                $l->platform,
                $l->device_type,
            ])->toArray();

        return [['Date/Time', 'User', 'Action', 'Subject', 'Description', 'IP Address', 'Browser', 'Platform', 'Device'], $rows, $title];
    }

    protected function exportLoginLogs(): array
    {
        $rows = LoginLog::with('user')->latest()->limit(5000)->get()
            ->map(fn (LoginLog $l) => [
                $l->created_at?->format('Y-m-d H:i:s'),
                $l->user->name ?? $l->email,
                $l->status,
                $l->reason,
                $l->ip_address,
                $l->browser,
                $l->platform,
                $l->device_type,
            ])->toArray();

        return [['Date/Time', 'User', 'Status', 'Reason', 'IP Address', 'Browser', 'Platform', 'Device'], $rows, 'Login Logs'];
    }

    protected function exportErrorLogs(): array
    {
        $rows = ErrorLog::with('user')->latest()->limit(5000)->get()
            ->map(fn (ErrorLog $e) => [
                $e->created_at?->format('Y-m-d H:i:s'),
                $e->level,
                $e->exception_class,
                $e->message,
                $e->file . ':' . $e->line,
                $e->url,
                $e->user->name ?? 'Guest',
                $e->is_resolved ? 'Resolved' : 'Open',
            ])->toArray();

        return [['Date/Time', 'Level', 'Exception', 'Message', 'Location', 'URL', 'User', 'Status'], $rows, 'Error Logs'];
    }

    protected function exportApiLogs(): array
    {
        $rows = ApiLog::with('user')->latest()->limit(5000)->get()
            ->map(fn (ApiLog $a) => [
                $a->created_at?->format('Y-m-d H:i:s'),
                $a->user->name ?? 'Guest',
                $a->method,
                $a->endpoint,
                $a->response_status,
                $a->response_time_ms,
                $a->ip_address,
            ])->toArray();

        return [['Date/Time', 'User', 'Method', 'Endpoint', 'Status', 'Time (ms)', 'IP Address'], $rows, 'API Logs'];
    }

    protected function applyCommonFilters($query, Request $request): void
    {
        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }
        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }
        if ($ip = $request->get('ip_address')) {
            $query->where('ip_address', $ip);
        }
        if ($from = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $to);
        }
    }
}
