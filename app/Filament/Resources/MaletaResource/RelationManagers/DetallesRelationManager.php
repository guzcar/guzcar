<?php

namespace App\Filament\Resources\MaletaResource\RelationManagers;

use App\Models\Herramienta;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class DetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';

    protected static ?string $recordTitleAttribute = 'a';

    protected static ?string $inverseRelationship = 'b';

    protected static ?string $label = 'c';

    protected static ?string $pluralLabel = 'd';

    protected static ?string $modelLabel = 'Herramienta';

    protected static ?string $pluralModelLabel = 'f';

    protected static ?string $title = 'Herramientas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('herramienta_id')
                                    ->label('Herramienta')
                                    ->required()
                                    ->relationship('herramienta', 'nombre')
                                    ->searchable()
                                    ->disabledOn('edit')
                                    ->preload()
                                    ->live()
                                    ->rule(fn() => function (string $attribute, $value, \Closure $fail) {
                                        if (!$value)
                                            return;
                                        $stock = Herramienta::query()->whereKey($value)->value('stock');
                                        if ($stock !== null && $stock <= 0) {
                                            $fail('Stock insuficiente.');
                                        }
                                    }),
                                Placeholder::make('stock_info')
                                    ->label('Stock disponible')
                                    ->content(function (Get $get) {
                                        $id = $get('herramienta_id');
                                        if (!$id)
                                            return new HtmlString('<span class="fi-ta-placeholder text-sm leading-6 text-gray-400 dark:text-gray-500">Seleccione una herramienta</span>');
                                        $stock = Herramienta::query()->whereKey($id)->value('stock');
                                        return $stock === null ? '—' : (string) $stock;
                                    })
                                    ->visibleOn('create'),
                                TextInput::make('ultimo_estado')
                                    ->disabled()
                                    ->hiddenOn('create'),
                            ])
                            ->columns(1)
                            ->columnSpan(1),
                        FileUpload::make('evidencia_url')
                            ->label('Evidencia')
                            ->directory('maleta_detalles')
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->columns([
                TextColumn::make('herramienta.nombre')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                ImageColumn::make('evidencia_url')
                    ->placeholder('Sin evidencia')
                    ->label('Evidencia')
                    ->width(70)
                    ->height(70),
                TextColumn::make('ultimo_estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'OPERATIVO' => 'success',
                        'MERMA' => 'warning',
                        'PERDIDO' => 'danger',
                    })
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar herramienta'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button(),
                Tables\Actions\Action::make('viewImage')
                    ->button()
                    ->label('Ver')
                    ->icon('heroicon-s-eye')
                    ->color('gray')
                    ->disabled(fn ($record) => empty($record->evidencia_url))
                    ->url(fn ($record) => Storage::url($record->evidencia_url))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->button()
                    ->label('Quitar'),
                // Tables\Actions\RestoreAction::make()
                //     ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('generarActaEntrega')
                        ->label('Generar acta de entrega')
                        ->icon('heroicon-o-document-text')
                        ->action(function ($records) {
                            $ids = $records->pluck('id')->toArray();
                            $maletaId = $this->getOwnerRecord()->id;

                            $url = URL::route('pdf.maleta.detalles', [
                                'maleta' => $maletaId,
                                'detalles' => implode(',', $ids)
                            ]);

                            // Abrir en nueva pestaña usando JavaScript
                            $this->js("window.open('{$url}', '_blank')");
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
