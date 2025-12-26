<?php

use App\Http\Controllers\Api\DevolucaoController;
use Illuminate\Support\Facades\Route;

Route::apiResource('devolucoes', DevolucaoController::class)->names([
    'index' => 'api.devolucoes.index',
    'store' => 'api.devolucoes.store',
    'show' => 'api.devolucoes.show',
    'update' => 'api.devolucoes.update',
    'destroy' => 'api.devolucoes.destroy',
]);

