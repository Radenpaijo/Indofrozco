<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Observers\BarangMasukObserver;
use App\Observers\BarangKeluarObserver;
use App\Services\NotificationService;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        BarangMasuk::observe(BarangMasukObserver::class);
        BarangKeluar::observe(BarangKeluarObserver::class);

        // Share real-time notification alerts to all views extending layouts.app
        View::composer('layouts.app', function ($view) {
            $notifData = NotificationService::getNotifications();
            $view->with('systemNotifications', $notifData['items']);
            $view->with('notificationCount', $notifData['count']);
            $view->with('notificationCountsByType', $notifData['counts_by_type']);
        });
    }
}
