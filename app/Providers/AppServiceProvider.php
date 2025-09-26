<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\View\Composers\PengadaanNotificationComposer;

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
        Paginator::useBootstrapFive();

        // Register view composer for pengadaan notifications
        View::composer([
            'layouts.navigation-top',
            'permintaan.*',
            'barang-medis.*',
            'barang-masuk.*'
        ], PengadaanNotificationComposer::class);
    }
}
