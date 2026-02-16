<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CalendarEvent;
use App\Models\User;
use Filament\Notifications\Notification;

class SendCalendarNotifications extends Command
{
    protected $signature = 'calendar:notify';
    protected $description = 'Enviar notificaciones de eventos según fecha y hora exacta de aviso';

    public function handle()
    {
        $now = now();
        $count = 0;

        // ---------------------------------------------------------
        // 1. PRIMER AVISO (notification_date)
        // ---------------------------------------------------------
        $firstEvents = CalendarEvent::query()
            ->where('notification_date', '<=', $now)
            ->where('notification_sent', false)
            ->where('ends_at', '>=', $now)
            ->get();

        foreach ($firstEvents as $event) {
            // Enviamos con tipo 'first'
            $this->sendNotification($event, 'first');
            $event->update(['notification_sent' => true]);
            $count++;
        }

        // ---------------------------------------------------------
        // 2. SEGUNDO AVISO (second_notification_date)
        // ---------------------------------------------------------
        $secondEvents = CalendarEvent::query()
            ->whereNotNull('second_notification_date')
            ->where('second_notification_date', '<=', $now)
            ->where('second_notification_sent', false)
            ->where('ends_at', '>=', $now)
            ->get();

        foreach ($secondEvents as $event) {
            // Enviamos con tipo 'second'
            $this->sendNotification($event, 'second');
            $event->update(['second_notification_sent' => true]);
            $count++;
        }

        if ($count > 0) {
            $this->info("Se enviaron {$count} notificaciones.");
        }
    }

    /**
     * Función auxiliar para calcular destinatarios y enviar con estilos dinámicos
     */
    private function sendNotification(CalendarEvent $event, string $type)
    {
        // 1. Calcular Destinatarios
        $recipients = collect();

        if ($event->is_global) {
            if (empty($event->target_roles)) {
                $recipients = User::all();
            } else {
                try {
                    $recipients = User::role($event->target_roles)->get();
                } catch (\Throwable $e) {
                    \Log::error("Error filtrando roles: " . $e->getMessage());
                }
            }
        } else {
            if ($event->user) {
                $recipients->push($event->user);
            }
        }

        $recipients = $recipients->unique('id');

        if ($recipients->isEmpty())
            return;

        // 2. Definir Estilos según el TIPO de aviso
        if ($type === 'first') {
            // Estilo: Recordatorio Amigable
            $title = "Recordatorio: {$event->title}";
            $color = 'warning'; // Amarillo / Naranja
            $icon = 'heroicon-o-bell'; // Campana
            $bodyPrefix = "Tienes una actividad pendiente.";
        } else {
            // Estilo: URGENTE / Alerta
            $title = "¡URGENTE! {$event->title}";
            $color = 'danger'; // Rojo intenso
            $icon = 'heroicon-o-exclamation-triangle'; // Triángulo de peligro
            $bodyPrefix = "SEGUNDO AVISO: Atención requerida.";
        }

        // 3. Enviar Notificación
        Notification::make()
            ->title($title)
            ->body("{$bodyPrefix}\n" . ($event->description ?? "Vence el: " . $event->ends_at->format('d/m/Y H:i')))
            ->icon($icon)
            ->color($color) // Aquí se aplica el color del icono y la barra lateral
            ->actions([
                \Filament\Notifications\Actions\Action::make('ver')
                    ->label('Ver Panel')
                    ->url(route('filament.admin.pages.dashboard'))
                    ->button() // Botón relleno para resaltar
                    ->color($type === 'second' ? 'danger' : 'primary'),
            ])
            ->sendToDatabase($recipients);

        $this->info("Enviado aviso '{$type}' de ID {$event->id} a " . $recipients->count() . " usuarios.");
    }
}
