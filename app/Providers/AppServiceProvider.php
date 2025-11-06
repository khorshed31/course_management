<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ✅ Use Bootstrap 5 pagination templates
        Paginator::useBootstrap();

        // ✅ Share session-based data across all views
        view()->composer('*', function ($view) {

            if (Auth::check()) {
                // share data
                view()->share([
                    'slugs' => (session()->get('slugs') ?? []),
                ]);

                // forget or remove permission data
                if ($view->getName() == 'partials._footer') {
                    session()->forget('slugs');
                }
            } else {
                view()->share(['slugs' => []]);
            }
        });
    }
}
