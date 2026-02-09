<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CalendarEvent;
use App\Models\User;
use Filament\Notifications\Notification;

class SendCalendarNotifications extends Command
{
    protected $signature = 'calendar:notify';
    protected $description = 'Enviar notificaciones de eventos agendados para hoy';

    public function handle()
    {
        $now = now();
        
        $events = CalendarEvent::query()
            ->whereDate('starts_at', $now->toDateString())
            ->where('starts_at', '<=', $now)
            ->where('notification_sent', false)
            ->get();

        foreach ($events as $event) {
            if ($event->is_global) {
                $recipients = User::all();
            } else {
                $recipients = User::where('id', $event->user_id)->get();
            }
            Notification::make()
                ->title('Recordatorio de Actividad')
                ->body("La actividad '{$event->title}' está programada para hoy.")
                ->icon('heroicon-o-calendar')
                ->warning()
                ->sendToDatabase($recipients);

            $event->update(['notification_sent' => true]);
            
            $this->info("Notificación enviada para: {$event->title}");
        }

        if ($events->isEmpty()) {
            $this->info('No hay eventos pendientes para notificar ahora.');
        }
    }
}
