<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function devolucoes(): HasMany
    {
        return $this->hasMany(Devolucao::class);
    }
}
