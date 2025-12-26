<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devolucao extends Model
{
    protected $table = 'devolucoes';

    protected $fillable = [
        'pedido_item_id',
        'cliente_id',
        'produto_id',
        'produto_troca_id',
        'quantidade',
        'motivo',
        'motivo_troca',
        'status',
        'tipo',
        'data_solicitacao',
        'data_status',
        'data_envio',
        'observacoes',
        'codigo_rastreamento',
    ];

    protected function casts(): array
    {
        return [
            'data_solicitacao' => 'datetime',
            'data_status' => 'datetime',
            'data_envio' => 'datetime',
        ];
    }

    public function pedidoItem(): BelongsTo
    {
        return $this->belongsTo(PedidoItem::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    public function produtoTroca(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'produto_troca_id');
    }

    public function historico(): HasMany
    {
        return $this->hasMany(DevolucaoHistorico::class);
    }

    public function lembretesEmail(): HasMany
    {
        return $this->hasMany(LembreteEmail::class);
    }

    public function reembolso(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Reembolso::class);
    }

    public function pedidoTroca(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Pedido::class, 'devolucao_id');
    }
}

