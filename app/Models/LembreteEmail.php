<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LembreteEmail extends Model
{
    protected $table = 'lembretes_email';

    protected $fillable = [
        'devolucao_id',
        'data_envio',
        'canal',
    ];

    protected function casts(): array
    {
        return [
            'data_envio' => 'datetime',
        ];
    }

    public function devolucao(): BelongsTo
    {
        return $this->belongsTo(Devolucao::class);
    }
}
