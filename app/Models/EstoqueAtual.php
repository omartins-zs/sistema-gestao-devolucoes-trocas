<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstoqueAtual extends Model
{
    protected $table = 'estoque_atual';

    protected $fillable = [
        'produto_id',
        'quantidade',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
