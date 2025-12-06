<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacao extends Model
{
    protected $table = 'notificacoes';

    protected $fillable = [
        'item_id',
        'tipo_alerta',
        'mensagem',
        'is_lida',
    ];

    protected $casts = [
        'is_lida' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Marca a notificação como lida
     */
    public function marcarComoLida(): void
    {
        $this->update(['is_lida' => true]);
    }
}
