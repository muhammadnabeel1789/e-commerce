<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Bagikan Pengaturan Toko ke SELURUH View (Global)
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $shopSettings = \Illuminate\Support\Facades\Cache::rememberForever('shop_settings', function () {
                return [
                    'shop_name'    => \App\Models\Setting::where('key', 'shop_name')->value('value') ?? 'Fashion Store',
                    'shop_phone'   => \App\Models\Setting::where('key', 'shop_phone')->value('value') ?? '-',
                    'shop_email'   => \App\Models\Setting::where('key', 'shop_email')->value('value') ?? '-',
                    'shop_address' => \App\Models\Setting::where('key', 'shop_address')->value('value') ?? '-',
                    'shop_logo'    => \App\Models\Setting::where('key', 'shop_logo')->value('value'),
                ];
            });
            $view->with($shopSettings);
        });
    }
}
