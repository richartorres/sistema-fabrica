<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoPdfController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/pedidos/{pedido}/pdf', [PedidoPdfController::class, 'descargar'])
    ->name('pedidos.pdf');
    
    Route::get('/pedidos/{pedido}/tirilla', [PedidoPdfController::class, 'tirilla'])
    ->name('pedidos.tirilla');