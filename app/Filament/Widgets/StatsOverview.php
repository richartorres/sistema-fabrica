<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use App\Models\Abono;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        // 1. Creamos la consulta base de pedidos aplicando el filtro si es Vendedor
        $pedidosQuery = Pedido::query();

        if ($user && $user->hasRole('Vendedor')) {
            $pedidosQuery->where('vendedor_id', $user->id);
        }

        // 2. Ventas Totales (Globales para Admin, filtradas para Vendedor)
        $ventasTotales = (clone $pedidosQuery)->sum('precio_total');

        // 3. Dinero Recaudado: Suma de los abonos cuyos pedidos pertenecen al usuario o a todos (Admin)
        $totalAbonado = Abono::whereHas('pedido', function ($query) use ($user) {
            if ($user && $user->hasRole('Vendedor')) {
                $query->where('vendedor_id', $user->id);
            }
        })->sum('monto');

        // 4. Saldo Pendiente real por cobrar
        $saldoPendiente = $ventasTotales - $totalAbonado;

        $esVendedor = $user && $user->hasRole('Vendedor');

        return [
            Stat::make('Ventas Totales (Pedidos)', '$ ' . number_format($ventasTotales, 0, ',', '.'))
                ->description($esVendedor ? 'Valor de tus pedidos' : 'Valor global de la producción ordenada')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),

            Stat::make('Dinero Recaudado (Abonos)', '$ ' . number_format($totalAbonado, 0, ',', '.'))
                ->description($esVendedor ? 'Tus abonos en caja' : 'Efectivo y transferencias en caja')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('Saldo Pendiente por Cobrar', '$ ' . number_format($saldoPendiente, 0, ',', '.'))
                ->description($esVendedor ? 'Tu cartera pendiente' : 'Pendiente contra entrega')
                ->descriptionIcon('heroicon-m-clock')
                ->color($saldoPendiente < 0 ? 'danger' : 'warning'),
        ];
    }
}