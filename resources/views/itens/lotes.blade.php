@extends('layouts.app')

@section('title', 'Lotes de ' . $item->nome)

@section('content')
<div>
    <div class="flex justify-between items-center mb-6 px-4 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Lotes de {{ $item->nome }}</h1>
            <p class="text-sm text-gray-600 mt-1">Quantidade Total: <strong>{{ $item->quantidade_atual }}</strong> {{ $item->unidade_medida }}</p>
        </div>
        <a href="{{ route('itens.index') }}" class="text-blue-600 hover:text-blue-900 cursor-pointer">
            ← Voltar para Listagem
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @php
        $lotesVencidos = $lotes->filter(fn($lote) => $lote->estaVencido() && $lote->quantidade > 0);
        $lotesProximos = $lotes->filter(fn($lote) => $lote->estaProximoVencimento() && !$lote->estaVencido() && $lote->quantidade > 0);
    @endphp

    @if($lotesVencidos->count() > 0)
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg w-full max-w-full">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-red-800">
                        ⚠️ {{ $lotesVencidos->count() }} {{ $lotesVencidos->count() == 1 ? 'Lote Vencido' : 'Lotes Vencidos' }}
                    </h3>
                    <p class="text-sm text-red-700 mt-1">
                        Total: {{ $lotesVencidos->sum('quantidade') }} {{ $item->unidade_medida }} vencidos
                    </p>
                </div>
                <form action="{{ route('itens.lotes.remover-vencidos', $item) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover os lotes vencidos do estoque?');">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out cursor-pointer">
                        Remover Vencidos
                    </button>
                </form>
            </div>
        </div>
    @endif

    @if($lotesProximos->count() > 0)
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg w-full max-w-full">
            <h3 class="text-lg font-semibold text-yellow-800">
                ⚠️ {{ $lotesProximos->count() }} {{ $lotesProximos->count() == 1 ? 'Lote Próximo do Vencimento' : 'Lotes Próximos do Vencimento' }}
            </h3>
            <p class="text-sm text-yellow-700 mt-1">
                Lotes que vencem nos próximos 30 dias
            </p>
        </div>
    @endif

    <!-- Card de Análise de Consumo -->
    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg w-full max-w-full">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">📊 Análise de Consumo</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
            <div class="bg-white p-3 rounded-lg shadow-sm">
                <div class="text-xs font-medium text-gray-500 uppercase">Média Mensal de Consumo (MMC)</div>
                <div class="text-2xl font-bold text-blue-600 mt-1">
                    {{ number_format($item->media_mensal_consumo, 1) }}
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    Baseado nos últimos 90 dias
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg shadow-sm">
                <div class="text-xs font-medium text-gray-500 uppercase">Estoque Mínimo Atual</div>
                <div class="text-2xl font-bold text-gray-700 mt-1">
                    {{ $item->estoque_minimo }}
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    {{ $item->unidade_medida }}
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg shadow-sm {{ $item->estoqueMinimoAbaixoSugerido() ? 'border-2 border-orange-500' : '' }}">
                <div class="text-xs font-medium text-gray-500 uppercase">Estoque Mínimo Sugerido</div>
                <div class="text-2xl font-bold {{ $item->estoqueMinimoAbaixoSugerido() ? 'text-orange-600' : 'text-green-600' }} mt-1">
                    {{ $item->estoque_minimo_sugerido }}
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    MMC × 1.5 (1,5 mês de estoque)
                    @if($item->estoqueMinimoAbaixoSugerido())
                        <span class="block text-orange-600 font-semibold mt-1">⚠️ Abaixo do sugerido</span>
                    @endif
                </div>
            </div>
        </div>
        @if($item->total_saidas_ultimos_90_dias > 0)
            <div class="mt-3 text-sm" style="color: #1f2937;">
                <strong>Total de saídas nos últimos 90 dias:</strong> {{ $item->total_saidas_ultimos_90_dias }} {{ $item->unidade_medida }}
            </div>
        @else
            <div class="mt-3 text-sm text-gray-500 italic">
                Não há movimentações de saída nos últimos 90 dias para calcular a MMC.
            </div>
        @endif
    </div>

    <div class="bg-white shadow-sm rounded-lg" style="overflow: visible;">
        <div class="table-container-scroll" style="overflow-x: auto !important; overflow-y: visible !important; -webkit-overflow-scrolling: touch !important; width: 100% !important; display: block !important;">
            <table class="min-w-full divide-y divide-gray-200" style="min-width: 600px;">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantidade
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data de Validade
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data de Entrada
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lotes as $lote)
                        <tr class="{{ $lote->estaVencido() && $lote->quantidade > 0 ? 'bg-red-100' : ($lote->estaProximoVencimento() && $lote->quantidade > 0 ? 'bg-yellow-50' : 'hover:bg-gray-50') }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $lote->quantidade }} {{ $item->unidade_medida }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $lote->data_validade->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($lote->quantidade == 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Esgotado
                                    </span>
                                @elseif($lote->estaVencido())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        🔴 Vencido
                                    </span>
                                @elseif($lote->estaProximoVencimento())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        ⚠️ Próximo do Vencimento
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        ✅ Válido
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ $lote->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhum lote cadastrado para este item.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Botão Ver Movimentações -->
    <div class="mt-6 flex justify-center">
        <a href="{{ route('itens.movimentacoes', $item) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out cursor-pointer flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Ver Movimentações
        </a>
    </div>
</div>

<style>
    /* Permitir scroll horizontal apenas no container da tabela */
    .table-container-scroll {
        overflow-x: auto !important;
        overflow-y: visible !important;
        -webkit-overflow-scrolling: touch !important;
        width: 100% !important;
        max-width: 100vw !important;
        display: block !important;
        position: relative !important;
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
    }

    /* Garantir que a tabela mantenha sua largura mínima */
    .table-container-scroll table {
        min-width: 600px !important;
        width: 100%;
    }

    /* Forçar que o container pai não bloqueie */
    .bg-white.shadow-sm.rounded-lg {
        overflow: visible !important;
    }
</style>

<script>
    // Permitir scroll horizontal na tabela removendo overflow-x-hidden do main
    document.addEventListener('DOMContentLoaded', function() {
        const main = document.querySelector('main');
        if (main) {
            main.style.overflowX = 'visible';
        }

        // Garantir que o container da tabela tenha scroll horizontal
        const tableContainer = document.querySelector('.table-container-scroll');
        if (tableContainer) {
            tableContainer.style.overflowX = 'auto';
            tableContainer.style.width = '100%';
        }
    });
</script>

@endsection

