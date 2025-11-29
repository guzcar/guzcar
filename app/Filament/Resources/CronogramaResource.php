<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CronogramaResource\Pages;
use App\Models\Cronograma;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class CronogramaResource extends Resource
{
    protected static ?string $model = Cronograma::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $navigationLabel = 'Cronograma';
    
    protected static ?string $pluralLabel = 'Cronogramas';
    
    protected static ?string $modelLabel = 'Asignación';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                Forms\Components\Select::make('tarea_id')
                    ->label('Tarea')
                    ->relationship('tarea', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->default(now()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCronograma::route('/'),
        ];
    }
}