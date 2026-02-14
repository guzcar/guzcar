<?php

namespace App\View\Components;

use App\Models\CalendarEvent;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GlobalEventsNotification extends Component
{
    public $events;

    public function __construct()
    {
        $user = auth()->user();
        $now = now();

        if (!$user) {
            $this->events = collect();
            return;
        }

        $userRoleNames = $user->getRoleNames()->toArray();

        // Buscamos eventos activos (en curso) que sean globales
        $this->events = CalendarEvent::query()
            ->where('starts_at', '<=', $now) // Ya empezó
            ->where('ends_at', '>=', $now)   // Aún no termina
            ->where('is_global', true)
            ->where(function ($query) use ($userRoleNames) {
                $query->whereNull('target_roles')
                      ->orWhere('target_roles', '[]')
                      // Filtro manual de JSON simple compatible con SQLite/MySQL básico
                      ->orWhereJsonContains('target_roles', $userRoleNames);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render(): View|string
    {
        return view('components.global-events-notification');
    }
}