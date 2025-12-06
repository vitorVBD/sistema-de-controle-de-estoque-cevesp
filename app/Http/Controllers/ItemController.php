<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Lote;
use App\Models\Movimentacao;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $itens = Item::all();
        return view('itens.index', compact('itens'));
    }

    /**
     * Display items with low stock, ordered by urgency
     */
    public function estoqueBaixo(): View
    {
        $itens = Item::whereColumn('quantidade_atual', '<=', 'estoque_minimo')
            ->orderByRaw('CASE WHEN quantidade_atual = 0 THEN 0 ELSE 1 END') // Itens zerados primeiro
            ->orderByRaw('(estoque_minimo - quantidade_atual) DESC') // Maior diferença primeiro
            ->get();

        return view('itens.index', compact('itens'));
    }

    /**
     * Display items with normal stock, ordered by proximity to minimum
     */
    public function estoqueNormal(): View
    {
        $itens = Item::whereColumn('quantidade_atual', '>', 'estoque_minimo')
            ->orderByRaw('(quantidade_atual - estoque_minimo) ASC') // Menor diferença primeiro (mais próximo do mínimo)
            ->get();

        return view('itens.index', compact('itens'));
    }

    /**
     * Display items approaching minimum stock (1 or 2 units above)
     */
    public function estoqueAproximando(): View
    {
        $itens = Item::whereRaw('quantidade_atual > estoque_minimo')
            ->whereRaw('quantidade_atual <= estoque_minimo + 2')
            ->orderByRaw('(quantidade_atual - estoque_minimo) ASC') // Menor diferença primeiro
            ->get();

        return view('itens.index', compact('itens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('itens.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'quantidade_atual' => 'required|integer|min:0',
            'estoque_minimo' => 'required|integer|min:0',
            'unidade_medida' => 'required|string|max:50',
            'data_validade_inicial' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $hoje = \Carbon\Carbon::today()->format('Y-m-d');
                        if ($value < $hoje) {
                            $fail('A data de validade deve ser hoje ou uma data futura.');
                        }
                    }
                },
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $item = Item::create([
                'nome' => $validated['nome'],
                'quantidade_atual' => $validated['quantidade_atual'],
                'estoque_minimo' => $validated['estoque_minimo'],
                'unidade_medida' => $validated['unidade_medida'],
            ]);

            // Se quantidade inicial > 0, cria um lote
            if ($validated['quantidade_atual'] > 0) {
                // Se foi informada validade, usa ela; caso contrário, usa 1 ano à frente
                $dataValidade = !empty($validated['data_validade_inicial'])
                    ? $validated['data_validade_inicial']
                    : \Carbon\Carbon::today()->addYear()->format('Y-m-d');

                Lote::create([
                    'item_id' => $item->id,
                    'quantidade' => $validated['quantidade_atual'],
                    'data_validade' => $dataValidade,
                ]);
            }
        });

        return redirect()->route('itens.index')
            ->with('success', 'Item criado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item): View
    {
        return view('itens.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'quantidade_atual' => 'required|integer|min:0',
            'estoque_minimo' => 'required|integer|min:0',
            'unidade_medida' => 'required|string|max:50',
        ]);

        $item->update($validated);

        return redirect()->route('itens.index')
            ->with('success', 'Item atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        return redirect()->route('itens.index')
            ->with('success', 'Item excluído com sucesso!');
    }

    /**
     * Exibe os lotes de um item
     */
    public function lotes(Item $item): View
    {
        $lotes = $item->lotes()
            ->orderBy('data_validade', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('itens.lotes', compact('item', 'lotes'));
    }

    /**
     * Remove lotes vencidos de um item
     */
    public function removerLotesVencidos(Item $item): RedirectResponse
    {
        $lotesVencidos = $item->lotes()
            ->where('data_validade', '<', \Carbon\Carbon::today())
            ->where('quantidade', '>', 0)
            ->get();

        $quantidadeTotalVencida = $lotesVencidos->sum('quantidade');

        DB::transaction(function () use ($item, $lotesVencidos, $quantidadeTotalVencida) {
            foreach ($lotesVencidos as $lote) {
                $item->decrement('quantidade_atual', $lote->quantidade);
                $lote->quantidade = 0;
                $lote->save();
            }

            // Cria movimentação de saída para registro no relatório de descarte/perdas
            if ($quantidadeTotalVencida > 0) {
                Movimentacao::create([
                    'item_id' => $item->id,
                    'tipo_movimentacao' => 'saida',
                    'quantidade' => $quantidadeTotalVencida,
                    'responsavel' => Auth::check() ? Auth::user()->name : 'Sistema',
                    'observacoes' => 'Descarte automático: itens vencidos removidos do estoque',
                ]);
            }
        });

        return redirect()->route('itens.lotes', $item)
            ->with('success', "Removidos {$quantidadeTotalVencida} itens vencidos do estoque.");
    }

    /**
     * Exibe as movimentações de um item específico
     */
    public function movimentacoes(Item $item): View
    {
        $movimentacoes = Movimentacao::where('item_id', $item->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('itens.movimentacoes', compact('item', 'movimentacoes'));
    }
}

