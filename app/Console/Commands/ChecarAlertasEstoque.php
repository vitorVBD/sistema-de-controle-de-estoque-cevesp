<?php

namespace App\Console\Commands;

use App\Events\NotificacaoCriada;
use App\Models\Item;
use App\Models\Lote;
use App\Models\Notificacao;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ChecarAlertasEstoque extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'estoque:checar-alertas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica e gera notificações para alertas de estoque (validade, estoque mínimo, sugestão MMC)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando alertas de estoque...');

        $this->verificarValidade();
        $this->verificarEstoqueMinimo();
        $this->verificarSugestaoMMC();

        $this->info('Verificação concluída!');
    }

    /**
     * Verifica itens que vencem em <= 30 dias
     */
    private function verificarValidade(): void
    {
        $dataLimite = Carbon::today()->addDays(30);

        $lotesProximos = Lote::where('data_validade', '<=', $dataLimite)
            ->where('data_validade', '>=', Carbon::today())
            ->where('quantidade', '>', 0)
            ->with('item')
            ->get();

        foreach ($lotesProximos as $lote) {
            $diasRestantes = Carbon::today()->diffInDays($lote->data_validade);

            // Verifica se já existe notificação não lida para este item e tipo
            $existeNotificacao = Notificacao::where('item_id', $lote->item_id)
                ->where('tipo_alerta', 'validade')
                ->where('is_lida', false)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if (!$existeNotificacao) {
                $notificacao = Notificacao::create([
                    'item_id' => $lote->item_id,
                    'tipo_alerta' => 'validade',
                    'mensagem' => "O item '{$lote->item->nome}' possui lote vencendo em {$diasRestantes} dia(s). Validade: " . $lote->data_validade->format('d/m/Y'),
                    'is_lida' => false,
                ]);

                // Dispara evento para WebSocket
                event(new NotificacaoCriada($notificacao));
            }
        }

        $this->info("Verificados " . $lotesProximos->count() . " lotes próximos do vencimento.");
    }

    /**
     * Verifica itens com estoque mínimo
     */
    private function verificarEstoqueMinimo(): void
    {
        $itensEstoqueMinimo = Item::whereColumn('quantidade_atual', '<=', 'estoque_minimo')
            ->get();

        foreach ($itensEstoqueMinimo as $item) {
            // Verifica se já existe notificação não lida para este item e tipo
            $existeNotificacao = Notificacao::where('item_id', $item->id)
                ->where('tipo_alerta', 'estoque_minimo')
                ->where('is_lida', false)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if (!$existeNotificacao) {
                $notificacao = Notificacao::create([
                    'item_id' => $item->id,
                    'tipo_alerta' => 'estoque_minimo',
                    'mensagem' => "O item '{$item->nome}' está com estoque baixo. Quantidade atual: {$item->quantidade_atual} {$item->unidade_medida} (mínimo: {$item->estoque_minimo})",
                    'is_lida' => false,
                ]);

                // Dispara evento para WebSocket
                event(new NotificacaoCriada($notificacao));
            }
        }

        $this->info("Verificados " . $itensEstoqueMinimo->count() . " itens com estoque mínimo.");
    }

    /**
     * Verifica se estoque mínimo está 25% menor que o sugerido pelo MMC
     */
    private function verificarSugestaoMMC(): void
    {
        $itens = Item::all();

        foreach ($itens as $item) {
            $mmc = $item->media_mensal_consumo;

            // Só verifica se houver consumo (MMC > 0)
            if ($mmc > 0) {
                $sugerido = $item->estoque_minimo_sugerido;
                $atual = $item->estoque_minimo;

                // Verifica se o atual é 25% menor que o sugerido
                $limite = $sugerido * 0.75;

                if ($atual < $limite) {
                    // Verifica se já existe notificação não lida para este item e tipo
                    $existeNotificacao = Notificacao::where('item_id', $item->id)
                        ->where('tipo_alerta', 'sugestao_mmc')
                        ->where('is_lida', false)
                        ->whereDate('created_at', Carbon::today())
                        ->exists();

                    if (!$existeNotificacao) {
                        $notificacao = Notificacao::create([
                            'item_id' => $item->id,
                            'tipo_alerta' => 'sugestao_mmc',
                            'mensagem' => "O estoque mínimo do item '{$item->nome}' está abaixo do sugerido. Atual: {$atual} | Sugerido: {$sugerido} (baseado em MMC: " . number_format($mmc, 1) . ")",
                            'is_lida' => false,
                        ]);

                        // Dispara evento para WebSocket
                        event(new NotificacaoCriada($notificacao));
                    }
                }
            }
        }

        $this->info("Verificadas sugestões MMC para " . $itens->count() . " itens.");
    }
}
