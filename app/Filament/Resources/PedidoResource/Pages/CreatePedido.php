<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use Filament\Actions;
use App\Filament\Resources\PedidoResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePedido extends CreateRecord
{
    protected static string $resource = PedidoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['vendedor_id'] = auth()->id();
        return $data;
    }
}
