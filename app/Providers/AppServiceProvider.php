<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Observers\BarangMasukObserver;
use App\Observers\BarangKeluarObserver;

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
    }
}
