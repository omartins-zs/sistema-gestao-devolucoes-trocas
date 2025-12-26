<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevolucaoHistorico extends Model
{
    protected $table = 'devolucao_historico';

    protected $fillable = [
        'devolucao_id',
        'status_old',
        'status_new',
        'alterado_por',
        'data_alteracao',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data_alteracao' => 'datetime',
        ];
    }

    public function devolucao(): BelongsTo
    {
        return $this->belongsTo(Devolucao::class);
    }

    public function alteradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'alterado_por');
    }
}
