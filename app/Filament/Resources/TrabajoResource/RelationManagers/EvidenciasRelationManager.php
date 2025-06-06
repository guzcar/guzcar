<?php

namespace App\Filament\Resources\TrabajoResource\RelationManagers;

use App\Models\Evidencia;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EvidenciasRelationManager extends RelationManager
{
    protected static string $relationship = 'evidencias_2';

    protected static ?string $title = 'Evidencias';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        FileUpload::make('evidencia_url')
                            ->imageEditor()
                            ->label('Evidencias')
                            ->directory('evidencia')
                            ->required()
                            ->multiple(fn(string $operation): bool => $operation === 'create')
                            ->reorderable(fn(string $operation): bool => $operation === 'create')
                            ->appendFiles()
                            ->panelLayout('grid')
                            ->columnSpan(1)
                            ->maxSize(500 * 1024),
                        Grid::make()
                            ->schema([
                                Select::make('user_id')
                                    ->label('Seleccionar Técnico')
                                    ->relationship('user', 'name', fn($query) => $query->withTrashed())
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->searchable()
                                    ->preload()
                                    // ->createOptionForm([
                                    //     TextInput::make('name')
                                    //         ->label('Nombre')
                                    //         ->required()
                                    //         ->maxLength(255),
                                    //     TextInput::make('email')
                                    //         ->label('Correo electrónico')
                                    //         ->unique(ignoreRecord: true)
                                    //         ->email()
                                    //         ->required()
                                    //         ->maxLength(255),
                                    //     TextInput::make('password')
                                    //         ->label('Contraseña')
                                    //         ->password()
                                    //         ->confirmed()
                                    //         ->dehydrated(fn($state) => filled($state))
                                    //         ->required()
                                    //         ->minLength(8),
                                    //     TextInput::make('password_confirmation')
                                    //         ->label('Confirmar contraseña')
                                    //         ->password()
                                    //         ->dehydrated(fn($state) => filled($state))
                                    //         ->required()
                                    //         ->minLength(8),
                                    // ])
                                    // ->createOptionUsing(function (array $data): int {
                                    //     $data['password'] = bcrypt($data['password']);
                                    //     return User::create($data)->getKey();
                                    // })
                                    // ->editOptionForm([
                                    //     TextInput::make('name')
                                    //         ->label('Nombre')
                                    //         ->required()
                                    //         ->maxLength(255),
                                    //     TextInput::make('email')
                                    //         ->label('Correo electrónico')
                                    //         ->unique(ignoreRecord: true)
                                    //         ->email()
                                    //         ->required()
                                    //         ->maxLength(255),
                                    //     TextInput::make('password')
                                    //         ->label('Contraseña')
                                    //         ->password()
                                    //         ->confirmed()
                                    //         ->dehydrated(fn($state) => filled($state))
                                    //         ->minLength(8),
                                    //     TextInput::make('password_confirmation')
                                    //         ->label('Confirmar contraseña')
                                    //         ->password()
                                    //         ->dehydrated(fn($state) => filled($state))
                                    //         ->minLength(8),
                                    // ])
                                    // ->getOptionLabelUsing(function ($value): ?string {
                                    //     $user = User::withTrashed()->find($value);
                                    //     return $user ? $user->name : 'Usuario eliminado';
                                    // }),
                                    ->required(),
                                Textarea::make('observacion'),
                            ])
                            ->columns(1)
                            ->columnSpan(1)
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->columns([
                ImageColumn::make('evidencia_url')
                    ->size(150)
                    ->label('Evidencia')
                    ->getStateUsing(function (Evidencia $record): string {
                        if ($record->tipo === 'imagen') {
                            return $record->evidencia_url;
                        }
                        return asset('images/video.png');
                    })
                    ->alignCenter()
                    ->verticallyAlignCenter(),
                TextColumn::make('mostrar')
                    ->label('Mostrar')
                    ->formatStateUsing(fn($state) => $state ? 'Si' : 'No')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->alignCenter(),
                TextColumn::make('user.name')
                    ->label('Subido por'),
                TextColumn::make('observacion')
                    ->label('Observación')
                    ->placeholder('Sin obserbación')
                    ->wrap()
                    ->lineClamp(3),
                TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->label('Fecha de edición')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->paginatedWhileReordering()
            ->defaultSort('sort', 'asc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model) {
                        // Obtener los archivos subidos
                        $archivos = $data['evidencia_url'];
                        $descripcion = $data['observacion'] ?? null;
                        $user_id = $data['user_id']; // Asegúrate de obtener el user_id
            
                        // Crear un registro para cada archivo
                        $primerModelo = null;
                        foreach ($archivos as $index => $archivo) {
                            $evidencia = Evidencia::create([
                                'trabajo_id' => $this->getOwnerRecord()->id,
                                'user_id' => $user_id, // Asignar el user_id
                                'evidencia_url' => $archivo,
                                'observacion' => $index === 0 ? $descripcion : null, // Solo la primera descripción
                            ]);

                            // Guardar el primer modelo creado
                            if ($index === 0) {
                                $primerModelo = $evidencia;
                            }
                        }

                        // Retornar el primer modelo creado
                        return $primerModelo;
                    })
                    ->successNotificationTitle('Evidencias subidas correctamente'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
                BulkActionGroup::make([
                    BulkAction::make('marcarComoSi')
                        ->label('Incluir en el Informe')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->mostrar = true; // Cambiar a "SI"
                                $record->save();
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('marcarComoNo')
                        ->label('Excluir del Informe')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->mostrar = false; // Cambiar a "NO"
                                $record->save();
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
