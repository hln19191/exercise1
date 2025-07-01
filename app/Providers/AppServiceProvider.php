<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

use Inertia\Inertia;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
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
        Vite::prefetch(concurrency: 3);
        Inertia::share([
            'general' => function () { //this is not used anymore
                return Lang::get('general');
            },
            'alertTimer' => config('app.alert_timer'),
            'permissions' => function () {
                if (!Auth::check()) {
                    return [];
                }

                /** @var \App\Models\User $user */
                $user = Auth::user();
                return $user->getAllPermissions()->pluck('name');
            }
            // 'permissions' => fn () => auth()->check()
            //    ? auth()->user()->getAllPermissions()->pluck('name')
            //: [],
        ]);
    }
}
