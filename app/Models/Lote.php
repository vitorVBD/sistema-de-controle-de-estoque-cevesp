<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Lote extends Model
{
    protected $fillable = [
        'item_id',
        'quantidade',
        'data_validade',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'data_validade' => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function estaVencido(): bool
    {
        return $this->data_validade->format('Y-m-d') < Carbon::today()->format('Y-m-d');
    }

    public function estaProximoVencimento(int $dias = 30): bool
    {
        return !$this->estaVencido() && $this->data_validade->format('Y-m-d') <= Carbon::today()->addDays($dias)->format('Y-m-d');
    }
}
