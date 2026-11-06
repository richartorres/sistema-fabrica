<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use App\Models\Abono;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{

    public static function canView(): bool
    {
        // Solo el Administrador puede ver las estadísticas financieras
        return auth()->user()?->hasRole('Administrador') ?? false;
    }

    protected function getStats(): array
    {
        // 1. Ventas Totales de todos los pedidos registrados
        $ventasTotales = Pedido::sum('precio_total');

        // 2. Dinero Recaudado: Suma de los abonos que están asociados a un pedido existente
        $totalAbonado = Abono::whereHas('pedido')->sum('monto');

        // 3. Saldo Pendiente real por cobrar
        $saldoPendiente = $ventasTotales - $totalAbonado;

        return [
            Stat::make('Ventas Totales (Pedidos)', '$ ' . number_format($ventasTotales, 0, ',', '.'))
                ->description('Valor global de la producción ordenada')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Dinero Recaudado (Abonos)', '$ ' . number_format($totalAbonado, 0, ',', '.'))
                ->description('Efectivo y transferencias en caja')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('Saldo Pendiente por Cobrar', '$ ' . number_format($saldoPendiente, 0, ',', '.'))
                ->description('Pendiente contra entrega')
                ->descriptionIcon('heroicon-m-clock')
                ->color($saldoPendiente < 0 ? 'danger' : 'warning'),
        ];
    }

        
}