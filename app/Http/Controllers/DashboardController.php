<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Movimentacao;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        $totalItens = Item::count();
        $itensEstoqueMinimo = Item::whereColumn('quantidade_atual', '<=', 'estoque_minimo')->count();

        // Itens se aproximando do estoque mínimo (1 ou 2 unidades acima)
        $itensAproximando = Item::whereRaw('quantidade_atual > estoque_minimo')
            ->whereRaw('quantidade_atual <= estoque_minimo + 2')
            ->count();

        $itensEstoqueNormal = $totalItens - $itensEstoqueMinimo - $itensAproximando;
        $totalMovimentacoes = Movimentacao::count();

        return view('dashboard.index', compact(
            'totalItens',
            'itensEstoqueMinimo',
            'itensAproximando',
            'itensEstoqueNormal',
            'totalMovimentacoes'
        ));
    }
}
