<?php

use App\Http\Controllers\Web\DevolucaoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('devolucoes.index');
});

Route::resource('devolucoes', DevolucaoController::class)->only(['index', 'show', 'update']);
Route::post('devolucoes/{id}/gerar-codigo-rastreamento', [DevolucaoController::class, 'gerarCodigoRastreamento'])->name('devolucoes.gerar-codigo');

Route::prefix('reembolsos')->name('reembolsos.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\ReembolsoController::class, 'index'])->name('index');
    Route::get('/{id}', [\App\Http\Controllers\Web\ReembolsoController::class, 'show'])->name('show');
    Route::post('/{id}/autorizar', [\App\Http\Controllers\Web\ReembolsoController::class, 'autorizar'])->name('autorizar');
    Route::post('/{id}/processar', [\App\Http\Controllers\Web\ReembolsoController::class, 'processar'])->name('processar');
});
