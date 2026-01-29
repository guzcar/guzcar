<?php

namespace App\Filament\Resources\EntregaEquipoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class DetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';

    protected static ?string $title = 'Implementos Entregados';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('implemento_id')
                    ->relationship('implemento', 'nombre')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('implemento.nombre')
                    ->label('Implemento')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('implemento.costo')
                    ->label('Costo'),
            ])
            ->filters([
            ])
            ->headerActions([
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('generarActaParcial')
                        ->label('Imprimir Selección')
                        ->icon('heroicon-o-printer')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            // $records son instancias de EntregaEquipoDetalle
                            $ids = $records->pluck('id')->toArray();

                            // Obtenemos el ID de la Entrega Padre
                            $entregaId = $this->getOwnerRecord()->id;

                            // Generamos la URL usando la ruta nueva que creamos
                            $url = URL::route('pdf.entrega_equipo.detalles', [
                                'entrega' => $entregaId,
                                'detalles' => implode(',', $ids)
                            ]);

                            // Abrimos pestaña con JS
                            $this->js("window.open('{$url}', '_blank')");
                        }),
                    ExportBulkAction::make('exportarExcel')
                        ->label('Exportar a Excel')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}