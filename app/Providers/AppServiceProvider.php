<?php

namespace App\Providers;

use App\Models\Master;
use App\Observers\MasterObserver;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
        Master::observe(MasterObserver::class);
        Inertia::share('translations', function () {
            $locale = app()->getLocale();
        
            $path = resource_path("lang/{$locale}.json");

            if (!File::exists($path)) {
                return [];
            }

            return [
                'locale' => $locale,
                'messages' => json_decode(File::get($path), true),
            ];
        });
        Vite::prefetch(concurrency: 3);
    }
}
