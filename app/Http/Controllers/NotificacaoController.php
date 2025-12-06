<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notificacao;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NotificacaoController extends Controller
{
    /**
     * Exibe todas as notificações
     */
    public function index(Request $request): View
    {
        $query = Notificacao::with('item')
            ->orderBy('created_at', 'desc');

        // Filtro por tipo
        if ($request->has('tipo') && $request->tipo !== '') {
            $query->where('tipo_alerta', $request->tipo);
        }

        // Filtro por status (lida/não lida)
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_lida', (bool) $request->status);
        }

        $notificacoes = $query->paginate(20);

        return view('notificacoes.index', compact('notificacoes'));
    }

    /**
     * Retorna notificações não lidas (para AJAX/dropdown)
     */
    public function naoLidas(): \Illuminate\Http\JsonResponse
    {
        $notificacoes = Notificacao::with('item')
            ->where('is_lida', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notificacao) {
                return [
                    'id' => $notificacao->id,
                    'tipo_alerta' => $notificacao->tipo_alerta,
                    'mensagem' => $notificacao->mensagem,
                    'created_at' => $notificacao->created_at->toISOString(),
                    'item' => $notificacao->item ? [
                        'id' => $notificacao->item->id,
                        'nome' => $notificacao->item->nome,
                    ] : null,
                ];
            });

        $totalNaoLidas = Notificacao::where('is_lida', false)->count();

        return response()->json([
            'notificacoes' => $notificacoes,
            'total' => $totalNaoLidas,
        ]);
    }

    /**
     * Marca uma notificação como lida
     */
    public function marcarComoLida(Request $request, Notificacao $notificacao): RedirectResponse|JsonResponse
    {
        $notificacao->marcarComoLida();

        // Se for requisição AJAX, retorna JSON
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            $totalNaoLidas = Notificacao::where('is_lida', false)->count();

            return response()->json([
                'success' => true,
                'message' => 'Notificação marcada como lida.',
                'total' => $totalNaoLidas,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Notificação marcada como lida.');
    }

    /**
     * Marca todas as notificações como lidas
     */
    public function marcarTodasComoLidas(): RedirectResponse
    {
        Notificacao::where('is_lida', false)->update(['is_lida' => true]);

        return redirect()->route('notificacoes.index')
            ->with('success', 'Todas as notificações foram marcadas como lidas.');
    }
}
