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
                $userRoleNames = method_exists($user, 'getRoleNames')
                    ? $user->getRoleNames()->toArray()
                    : [];

                $now = now();

                // 1. Traemos TODOS los eventos globales vigentes
                // Usamos notification_date para saber si ya debe mostrarse
                // Usamos ends_at para saber si ya expiró
                $candidates = CalendarEvent::query()
                    ->where('is_global', true)
                    // Si notification_date es null, usamos starts_at, si no, usamos notification_date
                    ->where(function ($q) use ($now) {
                        $q->where(function ($sub) use ($now) {
                            $sub->whereNotNull('notification_date')
                                ->where('notification_date', '<=', $now);
                        })->orWhere(function ($sub) use ($now) {
                            $sub->whereNull('notification_date')
                                ->where('starts_at', '<=', $now);
                        });
                    })
                    ->where('ends_at', '>=', $now)
                    ->orderBy('created_at', 'desc')
                    ->get();

                // 2. Filtramos estrictamente por Rol usando PHP (Más seguro que JSON SQL simple)
                $events = $candidates->filter(function ($event) use ($userRoleNames) {
                    // Si target_roles es null o array vacío [], es para TODOS
                    if (empty($event->target_roles)) {
                        return true;
                    }

                    // Si tiene restricciones, verificamos intersección
                    // Si el usuario NO tiene roles ($userRoleNames vacío) y el evento pide roles, devuelve false.
                    // Si el usuario tiene roles, verificamos si alguno coincide.
                    $rolesPermitidos = $event->target_roles; // Array guardado en BD

                    // array_intersect devuelve los valores coincidentes. Si hay > 0, tiene permiso.
                    return count(array_intersect($userRoleNames, $rolesPermitidos)) > 0;
                });

                // 3. Tomamos solo los últimos 5 para la campanita
                $events = $events->take(5);
            }

            $view->with('globalEvents', $events);
        });
    }
}
