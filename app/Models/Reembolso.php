<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reembolso extends Model
{
    protected $table = 'reembolsos';

    protected $fillable = [
        'devolucao_id',
        'cliente_id',
        'valor',
        'status',
        'autorizado',
        'autorizado_por',
        'data_autorizacao',
        'metodo',
        'observacoes',
        'data_processamento',
        'processado_por',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'autorizado' => 'boolean',
            'data_autorizacao' => 'datetime',
            'data_processamento' => 'datetime',
        ];
    }

    public function devolucao(): BelongsTo
    {
        return $this->belongsTo(Devolucao::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function processadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processado_por');
    }

    public function autorizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizado_por');
    }
}
