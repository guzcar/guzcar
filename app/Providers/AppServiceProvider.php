<?php

namespace App\Providers;

use App\Models\CalendarEvent;
use Illuminate\Support\Facades\View;
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
        View::composer('components.navbar', function ($view) {
            $user = auth()->user();
            $events = collect();

            if ($user) {
                $userRoleNames = $user->getRoleNames()->toArray();
                $now = now();

                $events = CalendarEvent::query()
                    ->where('starts_at', '<=', $now)
                    ->where('ends_at', '>=', $now)
                    ->where('is_global', true)
                    ->where(function ($query) use ($userRoleNames) {
                        $query->whereNull('target_roles')
                              ->orWhere('target_roles', '[]')
                              ->orWhereJsonContains('target_roles', $userRoleNames);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            $view->with('globalEvents', $events);
        });
    }
}
