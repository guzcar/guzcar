<?php

namespace App\Providers;

use App\Models\CalendarEvent;
use Illuminate\Support\Facades\URL;
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
        URL::forceScheme('https');
        View::composer('components.navbar', function ($view) {
            $user = auth()->user();
            $events = collect();

            if ($user) {
                $userRoleNames = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [];
                $now = now();

                // Traemos eventos vigentes (que no hayan terminado)
                // Y que ya haya pasado su fecha de notificación
                $candidates = CalendarEvent::query()
                    ->where('ends_at', '>=', $now)
                    ->where(function ($q) use ($now) {
                        $q->where('notification_date', '<=', $now)
                            ->orWhere('second_notification_date', '<=', $now);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();

                $events = $candidates->filter(function ($event) use ($userRoleNames, $user, $now) {
                    // 1. Permisos (Global vs Personal)
                    $hasPermission = false;
                    if ($event->is_global) {
                        if (empty($event->target_roles)) {
                            $hasPermission = true;
                        } else {
                            $hasPermission = count(array_intersect($userRoleNames, $event->target_roles)) > 0;
                        }
                    } else {
                        $hasPermission = $event->user_id === $user->id;
                    }

                    if (!$hasPermission)
                        return false;

                    // 2. Determinar la "Etapa" del aviso para el Modal (Stage)
                    // Si ya pasó la 2da fecha, la etapa es 'second'. Si no, es 'first'.
                    $event->notification_stage = 'first';
                    if ($event->second_notification_date && $now >= $event->second_notification_date) {
                        $event->notification_stage = 'second';
                    }

                    return true;
                });

                // Tomamos 5 para la lista de la campana
                $eventsForMenu = $events->take(5);

                // Para el modal, tomamos EL MÁS PRIORITARIO (por ejemplo, el que tenga etapa 'second' más reciente, o el creado más reciente)
                $eventForModal = $events->first();
            }

            $view->with('globalEvents', $eventsForMenu ?? collect());
            $view->with('modalEvent', $eventForModal ?? null);
        });
    }
}
