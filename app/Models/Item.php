<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Item extends Model
{
    protected $fillable = [
        'nome',
        'quantidade_atual',
        'estoque_minimo',
        'unidade_medida',
    ];

    protected $casts = [
        'quantidade_atual' => 'integer',
        'estoque_minimo' => 'integer',
    ];

    public function movimentacoes(): HasMany
    {
        return $this->hasMany(Movimentacao::class);
    }

    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class);
    }

    public function estaEmEstoqueMinimo(): bool
    {
        return $this->quantidade_atual <= $this->estoque_minimo;
    }

    /**
     * Verifica se o estoque está se aproximando do mínimo (1 ou 2 unidades acima)
     */
    public function estaAproximandoEstoqueMinimo(): bool
    {
        return $this->quantidade_atual > $this->estoque_minimo
            && $this->quantidade_atual <= $this->estoque_minimo + 2;
    }

    /**
     * Calcula a Média Mensal de Consumo (MMC) baseado nas saídas dos últimos 90 dias
     * Fórmula: MMC = Σ Quantidade de Saídas (90 dias) / 3
     */
    public function getMediaMensalConsumoAttribute(): float
    {
        $dataInicio = Carbon::now()->subDays(90);

        $totalSaidas = $this->movimentacoes()
            ->where('tipo_movimentacao', 'saida')
            ->where('created_at', '>=', $dataInicio)
            ->sum('quantidade');

        // Divide por 3 para obter a média mensal (90 dias = 3 meses)
        return round($totalSaidas / 3, 2);
    }

    /**
     * Sugere o Estoque Mínimo Ideal baseado na MMC
     * Sugestão: MMC × 1.5 (para cobrir 1,5 mês de consumo)
     */
    public function getEstoqueMinimoSugeridoAttribute(): int
    {
        $mmc = $this->media_mensal_consumo;

        if ($mmc == 0) {
            return $this->estoque_minimo; // Retorna o atual se não houver consumo
        }

        return (int) ceil($mmc * 1.5);
    }

    /**
     * Verifica se o estoque mínimo atual está abaixo do sugerido
     */
    public function estoqueMinimoAbaixoSugerido(): bool
    {
        return $this->estoque_minimo < $this->estoque_minimo_sugerido;
    }

    /**
     * Retorna a quantidade de saídas nos últimos 90 dias
     */
    public function getTotalSaidasUltimos90DiasAttribute(): int
    {
        $dataInicio = Carbon::now()->subDays(90);

        return $this->movimentacoes()
            ->where('tipo_movimentacao', 'saida')
            ->where('created_at', '>=', $dataInicio)
            ->sum('quantidade');
    }
}
