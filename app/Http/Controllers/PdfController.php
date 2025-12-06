<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Lote;
use App\Models\Movimentacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    /**
     * Gera relatório de Estoque Crítico
     * Itens onde quantidade_atual <= estoque_minimo
     */
    public function estoqueCritico()
    {
        $itens = Item::whereColumn('quantidade_atual', '<=', 'estoque_minimo')
            ->orderBy('quantidade_atual', 'asc')
            ->get();

        $data = [
            'itens' => $itens,
            'titulo' => 'Relatório de Estoque Crítico',
            'dataGeracao' => Carbon::now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('relatorios.estoque-critico', $data);
        return $pdf->download('estoque-critico-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Gera relatório de Consumo por Período
     * Movimentações de Saída dentro de um intervalo de datas
     */
    public function consumoPeriodo(Request $request)
    {
        $inicio = $request->input('inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fim = $request->input('fim', Carbon::now()->format('Y-m-d'));

        $movimentacoes = Movimentacao::with('item')
            ->where('tipo_movimentacao', 'saida')
            ->whereBetween('created_at', [
                Carbon::parse($inicio)->startOfDay(),
                Carbon::parse($fim)->endOfDay()
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Agrupa por item para calcular totais
        $consumoPorItem = [];
        foreach ($movimentacoes as $mov) {
            $itemId = $mov->item_id;
            if (!isset($consumoPorItem[$itemId])) {
                $consumoPorItem[$itemId] = [
                    'item' => $mov->item,
                    'total' => 0,
                    'movimentacoes' => []
                ];
            }
            $consumoPorItem[$itemId]['total'] += $mov->quantidade;
            $consumoPorItem[$itemId]['movimentacoes'][] = $mov;
        }

        $data = [
            'movimentacoes' => $movimentacoes,
            'consumoPorItem' => $consumoPorItem,
            'inicio' => Carbon::parse($inicio)->format('d/m/Y'),
            'fim' => Carbon::parse($fim)->format('d/m/Y'),
            'titulo' => 'Relatório de Consumo por Período',
            'dataGeracao' => Carbon::now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('relatorios.consumo-periodo', $data);
        return $pdf->download('consumo-periodo-' . Carbon::parse($inicio)->format('Y-m-d') . '-a-' . Carbon::parse($fim)->format('Y-m-d') . '.pdf');
    }

    /**
     * Gera relatório de Descarte e Perdas
     * Movimentações de Saída filtradas por observações que indiquem perda/descarte
     */
    public function descartePerdas(Request $request)
    {
        $inicio = $request->input('inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $fim = $request->input('fim', Carbon::now()->format('Y-m-d'));

        // Palavras-chave que indicam perda/descarte
        $palavrasChave = ['descarte', 'perda', 'vencido', 'estragado', 'danificado', 'quebrado', 'perdido'];

        $movimentacoes = Movimentacao::with('item')
            ->where('tipo_movimentacao', 'saida')
            ->whereBetween('created_at', [
                Carbon::parse($inicio)->startOfDay(),
                Carbon::parse($fim)->endOfDay()
            ])
            ->where(function ($query) use ($palavrasChave) {
                foreach ($palavrasChave as $palavra) {
                    $query->orWhere('observacoes', 'like', '%' . $palavra . '%');
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Agrupa por item para calcular totais
        $descartePorItem = [];
        foreach ($movimentacoes as $mov) {
            $itemId = $mov->item_id;
            if (!isset($descartePorItem[$itemId])) {
                $descartePorItem[$itemId] = [
                    'item' => $mov->item,
                    'total' => 0,
                    'movimentacoes' => []
                ];
            }
            $descartePorItem[$itemId]['total'] += $mov->quantidade;
            $descartePorItem[$itemId]['movimentacoes'][] = $mov;
        }

        $data = [
            'movimentacoes' => $movimentacoes,
            'descartePorItem' => $descartePorItem,
            'inicio' => Carbon::parse($inicio)->format('d/m/Y'),
            'fim' => Carbon::parse($fim)->format('d/m/Y'),
            'titulo' => 'Relatório de Descarte e Perdas',
            'dataGeracao' => Carbon::now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('relatorios.descarte-perdas', $data);
        return $pdf->download('descarte-perdas-' . Carbon::parse($inicio)->format('Y-m-d') . '-a-' . Carbon::parse($fim)->format('Y-m-d') . '.pdf');
    }

    /**
     * Gera relatório de Validade Próxima
     * Itens que vencem nos próximos 90 dias
     */
    public function validadeProxima()
    {
        $dataLimite = Carbon::now()->addDays(90);

        $lotes = Lote::with('item')
            ->where('quantidade', '>', 0)
            ->whereBetween('data_validade', [
                Carbon::now()->startOfDay(),
                $dataLimite->endOfDay()
            ])
            ->orderBy('data_validade', 'asc')
            ->get();

        // Agrupa por item
        $lotesPorItem = [];
        foreach ($lotes as $lote) {
            $itemId = $lote->item_id;
            if (!isset($lotesPorItem[$itemId])) {
                $lotesPorItem[$itemId] = [
                    'item' => $lote->item,
                    'lotes' => []
                ];
            }
            $lotesPorItem[$itemId]['lotes'][] = $lote;
        }

        $data = [
            'lotes' => $lotes,
            'lotesPorItem' => $lotesPorItem,
            'dataLimite' => $dataLimite->format('d/m/Y'),
            'titulo' => 'Relatório de Validade Próxima (90 dias)',
            'dataGeracao' => Carbon::now()->format('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('relatorios.validade-proxima', $data);
        return $pdf->download('validade-proxima-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
}

