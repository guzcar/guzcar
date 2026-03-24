<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\RepuestoCluster;
use Filament\Pages\SubNavigationPosition;
use App\Filament\Resources\CategoriaRepuestoResource\Pages;
use App\Models\CategoriaRepuesto;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class CategoriaRepuestoResource extends Resource
{
    protected static ?string $model = CategoriaRepuesto::class;

    protected static ?int $navigationSort = 75;
    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $modelLabel = 'Categoría';
    protected static ?string $pluralModelLabel = 'Categorías';
    protected static ?string $navigationLabel = 'Categorías';
    protected static ?string $cluster = RepuestoCluster::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    /**
     * Mapa para mostrar un swatch en el Select (modo claro).
     */
    protected static function extraColorHexMap(): array
    {
        return [
            'extra-color-1' => '#B4CBEF',
            'extra-color-2' => '#B2E3EF',
            'extra-color-3' => '#B4EFD8',
            'extra-color-4' => '#C6E9B3',
            'extra-color-5' => '#D9E9B4',
            'extra-color-6' => '#E5DDB1',
            'extra-color-7' => '#EED2B0',
            'extra-color-8' => '#EDCEAB',
            'extra-color-9' => '#EFB3B3',
            'extra-color-10' => '#EFB4CB',
            'extra-color-11' => '#E5B4EF',
            'extra-color-12' => '#C6B7F0',
            'extra-color-13' => '#BCB6EF',
            'extra-color-14' => '#B4C7EF',
            'extra-color-15' => '#B4D5EF',
            'extra-color-16' => '#ADEED7',
            'extra-color-17' => '#C5E3B1',
            'extra-color-18' => '#DCE9B4',
            'extra-color-19' => '#EED3AD',
            'extra-color-20' => '#DFC3C3',
            'extra-color-21' => '#B5CBEF',
            'extra-color-22' => '#B2EFE3',
            'extra-color-23' => '#BEEAB8',
            'extra-color-24' => '#EFCBB4',
            'extra-color-25' => '#EFC3B2',
            'extra-color-26' => '#F0B9B9',
            'extra-color-27' => '#E7B6EF',
            'extra-color-28' => '#B8BFF0',
            'extra-color-29' => '#B4DAEF',
            'extra-color-30' => '#CCEAB8',
            'extra-color-31' => '#D6D6D6',
        ];
    }

    protected static function extraColorOptionsHtml(): array
    {
        $options = [];

        foreach (self::extraColorHexMap() as $class => $hex) {
            $num = (int) str_replace('extra-color-', '', $class);

            $options[$class] = sprintf(
                '<span class="inline-flex items-center gap-2">
                <span class="inline-block h-4 w-4 rounded border border-gray-300 dark:border-gray-600 %s"></span>
                <span>Color %d</span>
            </span>',
                $class,
                $num,
            );
        }

        return $options;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    ->columnSpanFull(),

                // Usamos 'color' porque así se llama en tu migración
                Select::make('color')
                    ->label('Color')
                    ->options(self::extraColorOptionsHtml())
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->allowHtml()
                    ->required()
                    ->live()
                    ->columnSpanFull(),

                Placeholder::make('color_preview')
                    ->label('Vista previa')
                    ->content(function (Get $get): HtmlString {
                        $class = $get('color');

                        if (!$class) {
                            return new HtmlString('<span class="text-sm text-gray-500">Selecciona un color…</span>');
                        }

                        $num = (int) str_replace('extra-color-', '', $class);

                        return new HtmlString(sprintf(
                            '<span class="inline-flex items-center gap-2">
                                <span class="px-2 py-1 rounded text-xs font-semibold %s">Color %d</span>
                                <span class="text-xs text-gray-500">%s</span>
                            </span>',
                            e($class),
                            $num,
                            e($class),
                        ));
                    })
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchOnBlur(true)
            ->paginated([5, 10, 25, 50, 100])
            ->columns([
                TextColumn::make('color')
                    ->label('Color')
                    ->html()
                    ->formatStateUsing(function (?string $state): HtmlString {
                        if (!$state) {
                            return new HtmlString('<span class="text-gray-500">—</span>');
                        }

                        $num = (int) str_replace('extra-color-', '', $state);

                        return new HtmlString(sprintf(
                            '<span class="px-2 py-1 rounded text-xs font-semibold %s">Color %d</span>',
                            e($state),
                            $num
                        ));
                    }),

                TextColumn::make('nombre')
                    ->searchable(isIndividual: true)
                    ->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCategoriaRepuestos::route('/'),
        ];
    }
}
