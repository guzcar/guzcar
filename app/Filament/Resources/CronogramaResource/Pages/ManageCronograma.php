<?php

namespace App\Filament\Resources\CronogramaResource\Pages;

use App\Filament\Resources\CronogramaResource;
use App\Models\Cronograma;
use App\Models\CronogramaTarea;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\WithPagination;

class ManageCronograma extends Page
{
    use WithPagination;

    protected static string $resource = CronogramaResource::class;

    protected static string $view = 'filament.resources.cronograma-resource.pages.manage-cronograma';

    protected static ?string $title = 'Cronograma de Tareas';

    public $perPage = 15;
    public $sortColumn = 'fecha';
    public $sortDirection = 'desc';

    public function mount(): void
    {
        //
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Nueva Asignación')
                ->form([
                    Forms\Components\Select::make('user_id')
                        ->label('Usuario')
                        ->options(User::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                        
                    Forms\Components\Select::make('tarea_id')
                        ->label('Tarea')
                        ->options(CronogramaTarea::orderBy('nombre')->pluck('nombre', 'id'))
                        ->searchable()
                        ->required(),
                        
                    Forms\Components\DatePicker::make('fecha')
                        ->label('Fecha')
                        ->required()
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->default(now()),
                ])
                ->action(function (array $data) {
                    Cronograma::create($data);
                    
                    Notification::make()
                        ->title('Asignación creada')
                        ->success()
                        ->send();
                        
                    $this->resetPage();
                }),
        ];
    }

    public function getFechasConAsignaciones()
    {
        $query = Cronograma::select('fecha')
            ->distinct();

        // Aplicar ordenamiento
        $query->orderBy($this->sortColumn, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function getTareas()
    {
        return CronogramaTarea::orderBy('nombre')->get();
    }

    public function getUsuariosParaFecha($fecha, $tareaId)
    {
        // Buscar sin caché para asegurar datos actualizados
        $cronograma = Cronograma::where('fecha', $fecha)
            ->where('tarea_id', $tareaId)
            ->first();
            
        return $cronograma?->user_id ?? null;
    }

    public function updateAsignacion($fecha, $tareaId, $userId)
    {
        if (empty($userId)) {
            // Eliminar asignación
            $deleted = Cronograma::where('fecha', $fecha)
                ->where('tarea_id', $tareaId)
                ->delete();
                
            if ($deleted) {
                Notification::make()
                    ->title('Asignación eliminada')
                    ->body('Se quitó el usuario de esta tarea.')
                    ->success()
                    ->send();
                    
                // Verificar si quedan asignaciones en esta fecha
                $remaining = Cronograma::where('fecha', $fecha)->count();
                if ($remaining === 0) {
                    // Si no quedan asignaciones, recargar para que desaparezca la fila
                    $this->dispatch('$refresh');
                }
            }
            
            return;
        }
        
        // Verificar si ya existe una asignación
        $existente = Cronograma::where('fecha', $fecha)
            ->where('tarea_id', $tareaId)
            ->first();
        
        if ($existente) {
            // Si existe, actualizar (reemplazar usuario)
            $existente->update(['user_id' => $userId]);
            
            Notification::make()
                ->title('Asignación actualizada')
                ->body('Se reemplazó el usuario anterior por el nuevo.')
                ->warning()
                ->send();
        } else {
            // Si no existe, crear nueva
            Cronograma::create([
                'fecha' => $fecha,
                'tarea_id' => $tareaId,
                'user_id' => $userId,
            ]);
            
            Notification::make()
                ->title('Asignación creada')
                ->body('Usuario asignado correctamente.')
                ->success()
                ->send();
        }
        
        // Forzar recarga del componente para actualizar los selects
        $this->dispatch('$refresh');
    }

    public function duplicarDia($fecha)
    {
        $this->dispatch('open-modal', id: 'duplicar-dia-' . $fecha);
    }

    public function ejecutarDuplicarDia($fecha, $nuevaFecha)
    {
        $asignaciones = Cronograma::where('fecha', $fecha)->get();
        
        foreach ($asignaciones as $asignacion) {
            Cronograma::updateOrCreate(
                [
                    'fecha' => $nuevaFecha,
                    'tarea_id' => $asignacion->tarea_id,
                ],
                [
                    'user_id' => $asignacion->user_id,
                ]
            );
        }
        
        Notification::make()
            ->title('Día duplicado')
            ->body('Se duplicaron ' . $asignaciones->count() . ' asignaciones.')
            ->success()
            ->send();
            
        $this->resetPage();
    }

    public function eliminarDia($fecha)
    {
        $count = Cronograma::where('fecha', $fecha)->count();
        Cronograma::where('fecha', $fecha)->delete();
        
        Notification::make()
            ->title('Día eliminado')
            ->body("Se eliminaron {$count} asignaciones.")
            ->success()
            ->send();
            
        $this->resetPage();
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }
}