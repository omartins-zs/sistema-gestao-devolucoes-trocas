<?php

use App\Http\Controllers\Api\DevolucaoController;
use App\Http\Controllers\Api\ReembolsoController;
use Illuminate\Support\Facades\Route;

Route::apiResource('devolucoes', DevolucaoController::class)->names([
    'index' => 'api.devolucoes.index',
    'store' => 'api.devolucoes.store',
    'show' => 'api.devolucoes.show',
    'update' => 'api.devolucoes.update',
    'destroy' => 'api.devolucoes.destroy',
]);

Route::prefix('reembolsos')->name('api.reembolsos.')->group(function () {
    Route::get('/', [ReembolsoController::class, 'index'])->name('index');
    Route::get('/{id}', [ReembolsoController::class, 'show'])->name('show');
    Route::post('/{id}/autorizar', [ReembolsoController::class, 'autorizar'])->name('autorizar');
    Route::post('/{id}/processar', [ReembolsoController::class, 'processar'])->name('processar');
});

