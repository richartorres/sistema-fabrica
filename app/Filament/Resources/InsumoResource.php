<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InsumoResource\Pages;
use App\Models\Insumo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InsumoResource extends Resource
{
    protected static ?string $model = Insumo::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Inventario y Producción';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre del Insumo')
                    ->placeholder('Ej: Espuma D26 1.40m x 1.90m, Tela Jacquard, MDF 15mm')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('unidad_medida')
                    ->label('Unidad de Medida')
                    ->options([
                        'unidad' => 'Unidades',
                        'metro' => 'Metros (m)',
                        'lamina' => 'Láminas',
                        'kilo' => 'Kilos (kg)',
                        'rollo' => 'Rollos',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('precio_unitario')
                    ->label('Precio Unitario de Compra ($)')
                    ->numeric()
                    ->prefix('$')
                    ->required(),

                Forms\Components\TextInput::make('stock_actual')
                    ->label('Stock Actual')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\TextInput::make('stock_minimo')
                    ->label('Stock Mínimo (Alerta)')
                    ->numeric()
                    ->default(5)
                    ->helperText('Se mostrará una alerta si el stock cae por debajo de este valor.')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Insumo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unidad_medida')
                    ->label('Unidad')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('precio_unitario')
                    ->label('Precio U.')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_actual')
                    ->label('Stock Actual')
                    ->numeric()
                    ->sortable()
                    ->color(fn (Insumo $record): string => $record->stock_actual <= $record->stock_minimo ? 'danger' : 'success')
                    ->badge(),

                Tables\Columns\TextColumn::make('stock_minimo')
                    ->label('Stock Mínimo')
                    ->numeric(),
            ])
            ->filters([
                Tables\Filters\Filter::make('stock_bajo')
                    ->label('Stock Crítico (Bajo Mínimo)')
                    ->query(fn ($query) => $query->whereColumn('stock_actual', '<=', 'stock_minimo')),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInsumos::route('/'),
            'create' => Pages\CreateInsumo::route('/create'),
            'edit' => Pages\EditInsumo::route('/{record}/edit'),
        ];
    }
}