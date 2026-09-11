<?php

use App\Http\Middleware\LogActivity;
use App\Http\Middleware\LogApiActivity;
use App\Http\Middleware\SetLocale;
use App\Models\ErrorLog;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        $middleware->web(append: [
            SetLocale::class,
            LogActivity::class,
        ]);
         $middleware->api(append: [
             LogApiActivity::class,
         ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
        // Feed every reportable exception into the Error Logs feature (Activity & Audit).
        // Laravel already excludes noisy/expected exceptions (validation, 404, 419, auth)
        // from being "reported" by default, so this stays focused on real errors.
        $exceptions->report(function (\Throwable $e) {
            try {
                ErrorLog::record($e);
            } catch (\Throwable $loggingFailure) {
                // Never let audit logging itself break error handling.
            }
        });
    })->create();
