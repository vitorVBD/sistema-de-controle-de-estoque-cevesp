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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MovimentacaoController extends Controller
{
    /**
     * Exibe o histórico de movimentações
     */
    public function index(): View
    {
        $movimentacoes = Movimentacao::with('item')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('movimentacoes.index', compact('movimentacoes'));
    }

    /**
     * Exibe o formulário para registrar entrada
     */
    public function createEntrada(): View
    {
        $itens = Item::orderBy('nome')->get();
        return view('movimentacoes.entrada', compact('itens'));
    }

    /**
     * Exibe o formulário para registrar saída
     */
    public function createSaida(): View
    {
        $itens = Item::where('quantidade_atual', '>', 0)
            ->orderBy('nome')
            ->get();
        return view('movimentacoes.saida', compact('itens'));
    }
    /**
     * Registra uma entrada de estoque
     */
    public function registrarEntrada(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantidade' => 'required|integer|min:1',
            'data_validade' => [
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
            'observacoes' => 'nullable|string|max:1000',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        DB::transaction(function () use ($validated, $item) {
            // Se não foi informada validade, usa 1 ano à frente como padrão
            $dataValidade = !empty($validated['data_validade'])
                ? $validated['data_validade']
                : \Carbon\Carbon::today()->addYear()->format('Y-m-d');

            // Cria o lote com validade
            Lote::create([
                'item_id' => $item->id,
                'quantidade' => $validated['quantidade'],
                'data_validade' => $dataValidade,
            ]);

            // Cria o registro de movimentação
            Movimentacao::create([
                'item_id' => $item->id,
                'tipo_movimentacao' => 'entrada',
                'quantidade' => $validated['quantidade'],
                'responsavel' => Auth::check() ? Auth::user()->name : 'Sistema',
                'observacoes' => $validated['observacoes'] ?? null,
            ]);

            // Atualiza a quantidade atual do item
            $item->increment('quantidade_atual', $validated['quantidade']);
        });

        return redirect()->route('dashboard')
            ->with('success', 'Entrada registrada com sucesso!');
    }

    /**
     * Registra uma saída de estoque (usa FIFO - First In First Out)
     */
    public function registrarSaida(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantidade' => 'required|integer|min:1',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        if ($validated['quantidade'] > $item->quantidade_atual) {
            throw ValidationException::withMessages([
                'quantidade' => 'A quantidade solicitada (' . $validated['quantidade'] . ') excede a quantidade disponível (' . $item->quantidade_atual . ').',
            ]);
        }

        DB::transaction(function () use ($validated, $item) {
            $quantidadeRestante = $validated['quantidade'];

            // Busca lotes ordenados por data de criação (FIFO) e depois por validade (mais antigos primeiro)
            $lotes = Lote::where('item_id', $item->id)
                ->where('quantidade', '>', 0)
                ->orderBy('created_at', 'asc')
                ->orderBy('data_validade', 'asc')
                ->get();

            // Remove dos lotes mais antigos primeiro
            foreach ($lotes as $lote) {
                if ($quantidadeRestante <= 0) {
                    break;
                }

                if ($lote->quantidade <= $quantidadeRestante) {
                    $quantidadeRestante -= $lote->quantidade;
                    $lote->quantidade = 0;
                    $lote->save();
                } else {
                    $lote->quantidade -= $quantidadeRestante;
                    $lote->save();
                    $quantidadeRestante = 0;
                }
            }

            // Cria o registro de movimentação
            Movimentacao::create([
                'item_id' => $item->id,
                'tipo_movimentacao' => 'saida',
                'quantidade' => $validated['quantidade'],
                'responsavel' => Auth::check() ? Auth::user()->name : 'Sistema',
                'observacoes' => $validated['observacoes'] ?? null,
            ]);

            // Atualiza a quantidade atual do item
            $item->decrement('quantidade_atual', $validated['quantidade']);
        });

        return redirect()->route('dashboard')
            ->with('success', 'Saída registrada com sucesso!');
    }
}

