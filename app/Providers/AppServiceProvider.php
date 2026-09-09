<?php

namespace App\Providers;

use App\Models\AppNotification;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register repository interface -> implementation bindings.
     * Add one line here for every new module's repository as it's built.
     */
    public function register(): void
    {
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
    }

    public function boot(): void
    {

        Schema::defaultStringLength(191);

        View::composer('layouts.app', function ($view) {
            if (! auth()->check()) {
                return;
            }

            $view->with([
                'unreadNotificationsCount' => AppNotification::where('user_id', auth()->id())->unread()->count(),
                'recentNotifications' => AppNotification::where('user_id', auth()->id())
                    ->latest()->limit(6)->get(),
            ]);
        });
    }
}
