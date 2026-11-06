<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Models\Pedido;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Ventas y Producción';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Cliente y Venta')
                    ->schema([
                        Forms\Components\Select::make('cliente_id')
                            ->label('Cliente / Mayorista')
                            ->relationship('cliente', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\DatePicker::make('fecha_entrega_prometida')
                            ->label('Fecha de Entrega Compromiso')
                            ->default(now()->addDays(3))
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Estado Comercial y de Producción')
                    ->schema([
                        Forms\Components\Select::make('estado')
                            ->label('Estado de Producción')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'en_fabricacion' => 'En Fabricación',
                                'entregado' => 'Entregado',
                                'cancelado' => 'Cancelado',
                            ])
                            ->default('pendiente')
                            ->required(),

                        Forms\Components\Select::make('estado_pago')
                            ->label('Estado de Pago')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'abono' => 'Abonado (50%)',
                                'pagado' => 'Pagado Total',
                            ])
                            ->default('pendiente')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Totales')
                    ->schema([
                        Forms\Components\TextInput::make('precio_total')
                            ->label('Monto Total ($)')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                Forms\Components\TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->default(1)
                            ->required(),

                 
                Forms\Components\TextInput::make('precio_unitario')
                            ->label('Precio Unitario ($)')
                            ->numeric()
                            ->prefix('$')
                            ->required(),            


                        Forms\Components\Textarea::make('notas')
                            ->label('Notas de Fabricación')
                            ->placeholder('Ej: Colchón 1.40x1.90 en tela Jacquard color gris, pilo adicional.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('# Pedido')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Producción')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'en_fabricacion' => 'info',
                        'entregado' => 'success',
                        'cancelado' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('estado_pago')
                    ->label('Pago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'danger',
                        'abono' => 'warning',
                        'pagado' => 'success',
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Filtrar por Producción')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_fabricacion' => 'En Fabricación',
                        'entregado' => 'Entregado',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('descargar_pdf')
            ->label('PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->url(fn (Pedido $record): string => route('pedidos.pdf', $record))
            ->openUrlInNewTab(),

                Tables\Actions\Action::make('descargar_tirilla')
            ->label('Tirilla')
            ->icon('heroicon-o-receipt-percent')
            ->color('warning')
            ->url(fn (Pedido $record): string => route('pedidos.tirilla', $record))
            ->openUrlInNewTab(),

            

            Tables\Actions\Action::make('registrar_abono')
            ->label('Abonar')
            ->icon('heroicon-o-currency-dollar')
            ->color('info')
            ->form([
                Forms\Components\TextInput::make('monto')
                    ->label('Monto del Abono')
                    ->numeric()
                    ->required()
                    ->prefix('$')
                    ->rules([
                        function ($record) {
                            return function (string $attribute, $value, \Closure $fail) use ($record) {
                                // Calculamos el saldo pendiente actual del pedido
                                $totalAbonado = $record->abonos()->sum('monto');
                                $saldoPendiente = $record->precio_total - $totalAbonado;

                                if ($value > $saldoPendiente) {
                                    $fail("El abono no puede superar el saldo pendiente de \$ " . number_format($saldoPendiente, 0, ',', '.'));
                                }
                            };
                        },
                    ]),
                Forms\Components\Select::make('metodo_pago')
                    ->label('Método de Pago')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'transferencia' => 'Transferencia',
                        'tarjeta' => 'Tarjeta',
                    ])
                    ->default('efectivo')
                    ->required(),
                Forms\Components\Textarea::make('notas')
                    ->label('Notas / Comprobante')
                    ->placeholder('Número de comprobante o detalles adicionales'),
            ])
            ->action(function (Pedido $record, array $data): void {
                $record->abonos()->create([
                    'monto' => $data['monto'],
                    'metodo_pago' => $data['metodo_pago'],
                    'notas' => $data['notas'] ?? null,
                ]);

                \Filament\Notifications\Notification::make()
                    ->title('Abono registrado correctamente')
                    ->success()
                    ->send();
            }),
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
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }
}