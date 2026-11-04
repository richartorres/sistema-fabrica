<?php

use App\Http\Controllers\CompraController;
use Illuminate\Support\Facades\Route;

Route::post('/compras', [CompraController::class, 'store']);
Route::post('/compras/{id}/abonos', [CompraController::class, 'registrarAbono']);

Route::get('/compras', [CompraController::class, 'index']);
Route::get('/compras/pendientes', [CompraController::class, 'pendientes']);
