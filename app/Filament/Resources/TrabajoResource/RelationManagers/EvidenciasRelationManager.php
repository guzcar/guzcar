<?php

namespace App\Filament\Resources\TrabajoResource\RelationManagers;

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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EvidenciasRelationManager extends RelationManager
{
    protected static string $relationship = 'evidencias';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        FileUpload::make('evidencia_url')
                            ->directory('evidencia')
                            ->required()
                            ->columnSpan(1),
                        Grid::make()
                            ->schema([
                                Select::make('user_id') // Campo mecanico_id
                                    ->label('Seleccionar Mecánico')
                                    ->relationship('user', 'name', fn($query) => $query->withTrashed()) // Relación hacia User desde TrabajoMecanico
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->searchable()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->label('Correo electrónico')
                                            ->unique(ignoreRecord: true)
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('password')
                                            ->label('Contraseña')
                                            ->password()
                                            ->confirmed()
                                            ->dehydrated(fn($state) => filled($state))
                                            ->required()
                                            ->minLength(8),
                                        TextInput::make('password_confirmation')
                                            ->label('Confirmar contraseña')
                                            ->password()
                                            ->dehydrated(fn($state) => filled($state))
                                            ->required()
                                            ->minLength(8),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        $data['password'] = bcrypt($data['password']);
                                        return User::create($data)->getKey();
                                    })
                                    ->editOptionForm([
                                        TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->label('Correo electrónico')
                                            ->unique(ignoreRecord: true)
                                            ->email()
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('password')
                                            ->label('Contraseña')
                                            ->password()
                                            ->confirmed()
                                            ->dehydrated(fn($state) => filled($state))
                                            ->minLength(8),
                                        TextInput::make('password_confirmation')
                                            ->label('Confirmar contraseña')
                                            ->password()
                                            ->dehydrated(fn($state) => filled($state))
                                            ->minLength(8),
                                    ])
                                    ->getOptionLabelUsing(function ($value): ?string {
                                        $user = User::withTrashed()->find($value);
                                        return $user ? $user->name : 'Usuario eliminado';
                                    })
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
            ->recordTitleAttribute('observacion')
            ->columns([
                ImageColumn::make('evidencia_url')
                    ->label('Evidencia')
                    ->alignCenter()
                    ->verticallyAlignCenter(),
                TextColumn::make('user.name')
                    ->label('Subido por'),
                TextColumn::make('observacion')
                    ->label('Observación')
                    ->wrap()
                    ->lineClamp(3),
                TextColumn::make('updated_at')
                    ->label('Última actualización')
                    ->dateTime('d/m/Y H:i:s')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
