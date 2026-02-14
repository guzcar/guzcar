<?php

namespace App\Filament\Widgets;

use App\Models\CalendarEvent;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class DashboardCalendarWidget extends FullCalendarWidget
{
    public Model|string|null $model = CalendarEvent::class;

    protected static ?int $sort = 11;

    public function fetchEvents(array $fetchInfo): array
    {
        $user = auth()->user();
        $userRoleNames = $user->getRoleNames()->toArray(); // Array de roles del usuario actual

        return CalendarEvent::query()
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
            ->where(function (Builder $query) use ($user, $userRoleNames) {
                // 1. Eventos propios del usuario
                $query->where('user_id', $user->id)
                    // 2. O Eventos Globales
                    ->orWhere(function ($q) use ($userRoleNames) {
                    $q->where('is_global', true)
                        ->where(function ($subQ) use ($userRoleNames) {
                            // 2a. Sin restricción de roles (visible para todos)
                            $subQ->whereNull('target_roles')
                                ->orWhere('target_roles', '[]')
                                // 2b. O el usuario tiene uno de los roles permitidos
                                // Nota: en JSON MySQL puro se usaría whereJsonOverlaps, 
                                // pero para compatibilidad simple iteramos o usamos lógica básica
                                ->orWhereJsonContains('target_roles', $userRoleNames);
                            // Si un usuario tiene múltiples roles, esto validará si al menos uno coincide
                        });
                });
            })
            ->get()
            ->map(
                fn(CalendarEvent $event) => [
                    'id' => $event->id,
                    'title' => $event->title . ($event->is_global ? ' (Global)' : ''),
                    'start' => $event->starts_at,
                    'end' => $event->ends_at,
                    'backgroundColor' => $event->is_global ? '#ef4444' : '#3b82f6',
                    'borderColor' => $event->is_global ? '#ef4444' : '#3b82f6',
                ]
            )
            ->all();
    }

    public function config(): array
    {
        return [
            'displayEventTime' => false,
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('title')
                        ->label('Nombre de la actividad')
                        ->required(),

                    Toggle::make('is_global')
                        ->label('Global (Visible para otros)')
                        ->default(false)
                        ->inline(false)
                        ->live() // Hace que el formulario reaccione al cambio
                        ->hidden(fn(?CalendarEvent $record) => $record && $record->user_id !== auth()->id()),

                    // Nuevo campo Select para roles
                    Select::make('target_roles')
                        ->label('Visible solo para los roles (Dejar vacío para todos)')
                        ->options(Role::all()->pluck('name', 'name'))
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->visible(fn(Get $get) => $get('is_global')), // Solo visible si es global

                    DateTimePicker::make('starts_at')
                        ->label('Inicio')
                        ->required(),

                    DateTimePicker::make('ends_at')
                        ->label('Fin')
                        ->required(),
                ])
        ];
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->label('Crear Evento')
                ->mountUsing(
                    fn(\Filament\Forms\Form $form, array $arguments) =>
                    $form->fill([
                        'starts_at' => $arguments['start'] ?? null,
                        'ends_at' => $arguments['end'] ?? null
                    ])
                )
                ->form($this->getFormSchema())
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = auth()->id();
                    // Asegurar que si no es global, target_roles sea null
                    if (!($data['is_global'] ?? false)) {
                        $data['target_roles'] = null;
                    }
                    return $data;
                }),
        ];
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->modalHeading(
                    fn(CalendarEvent $record) =>
                    $record->user_id === auth()->id()
                    ? 'Editar Actividad'
                    : 'Detalles de la Actividad'
                )
                ->mountUsing(
                    fn(CalendarEvent $record, \Filament\Forms\Form $form) =>
                    $form->fill([
                        'title' => $record->title,
                        'is_global' => $record->is_global,
                        'starts_at' => $record->starts_at,
                        'ends_at' => $record->ends_at,
                    ])
                )
                ->form($this->getFormSchema())
                ->disabledForm(fn(CalendarEvent $record) => $record->user_id !== auth()->id())
                ->modalSubmitAction(function ($action, CalendarEvent $record) {
                    if ($record->user_id !== auth()->id()) {
                        return false;
                    }
                    return $action->label('Guardar');
                })
                ->modalCancelAction(function ($action, CalendarEvent $record) {
                    if ($record->user_id !== auth()->id()) {
                        return $action->label('Cerrar');
                    }
                    return $action->label('Cancelar');
                }),

            DeleteAction::make()
                ->label('Borrar')
                ->visible(fn(CalendarEvent $record) => $record->user_id === auth()->id()),
        ];
    }
}
