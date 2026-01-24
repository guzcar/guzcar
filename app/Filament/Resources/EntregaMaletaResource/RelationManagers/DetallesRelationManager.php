<?php

namespace App\Filament\Resources\EntregaMaletaResource\RelationManagers;

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

    protected static ?string $title = 'Herramientas Entregadas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('herramienta_id')
                    ->relationship('herramienta', 'nombre')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('herramienta.nombre')
                    ->label('Herramienta')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('herramienta.costo')
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
                            // $records son instancias de EntregaMaletaDetalle
                            $ids = $records->pluck('id')->toArray();

                            // Obtenemos el ID de la Entrega Padre
                            $entregaId = $this->getOwnerRecord()->id;

                            // Generamos la URL
                            $url = URL::route('pdf.entrega.detalles', [
                                'entrega' => $entregaId,
                                'detalles' => implode(',', $ids)
                            ]);

                            // Abrimos pestaña
                            $this->js("window.open('{$url}', '_blank')");
                        }),
                    ExportBulkAction::make('exportarExcel')
                        ->label('Exportar a Excel')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}