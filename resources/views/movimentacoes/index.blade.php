@extends('layouts.app')

@section('title', 'Histórico de Movimentações')

@section('content')
<div>
    <div class="flex justify-between items-center mb-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Histórico de Movimentações</h1>
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-900 cursor-pointer">
            ← Voltar ao Dashboard
        </a>
    </div>

    <!-- Seção de Relatórios -->
    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">📊 Relatórios de Movimentações</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 justify-items-center">
            <!-- Relatório de Consumo -->
            <div class="border border-orange-200 bg-orange-50 rounded-lg p-4 w-full max-w-md">
                <h3 class="font-semibold text-orange-900 mb-3 flex items-center">
                    <span class="text-xl mr-2">📄</span>
                    Relatório de Consumo por Período
                </h3>
                <p class="text-sm text-orange-700 mb-3">Gere um PDF com todas as saídas de estoque em um período específico.</p>
                <form action="{{ route('relatorios.consumo') }}" method="GET" target="_blank" class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-orange-900 mb-1">Data Início</label>
                            <input type="date" name="inicio" value="{{ \Carbon\Carbon::now()->subMonth()->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-orange-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-orange-900 mb-1">Data Fim</label>
                            <input type="date" name="fim" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-orange-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out cursor-pointer flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Gerar PDF
                    </button>
                </form>
            </div>

            <!-- Relatório de Descarte e Perdas -->
            <div class="border border-red-200 bg-red-50 rounded-lg p-4 w-full max-w-md">
                <h3 class="font-semibold text-red-900 mb-3 flex items-center">
                    <span class="text-xl mr-2">📄</span>
                    Relatório de Descarte e Perdas
                </h3>
                <p class="text-sm text-red-700 mb-3">Gere um PDF com saídas relacionadas a descartes, perdas e itens danificados.</p>
                <form action="{{ route('relatorios.descarte') }}" method="GET" target="_blank" class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-red-900 mb-1">Data Início</label>
                            <input type="date" name="inicio" value="{{ \Carbon\Carbon::now()->subMonth()->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-red-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-red-900 mb-1">Data Fim</label>
                            <input type="date" name="fim" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-red-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out cursor-pointer flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Gerar PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg" style="overflow: visible;">
        <div class="table-container-scroll" style="overflow-x: auto !important; overflow-y: visible !important; -webkit-overflow-scrolling: touch !important; width: 100% !important; display: block !important;">
            <table class="min-w-full divide-y divide-gray-200" style="min-width: 800px;">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data/Hora
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Item
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantidade
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Responsável
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Observações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($movimentacoes as $movimentacao)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $movimentacao->created_at->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $movimentacao->created_at->format('H:i:s') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $movimentacao->item->nome }}</div>
                                <div class="text-xs text-gray-500">{{ $movimentacao->item->unidade_medida }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($movimentacao->tipo_movimentacao === 'entrada')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        ➕ Entrada
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                        ➖ Saída
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    @if($movimentacao->tipo_movimentacao === 'entrada')
                                        <span class="text-green-600">+{{ $movimentacao->quantidade }}</span>
                                    @else
                                        <span class="text-orange-600">-{{ $movimentacao->quantidade }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $movimentacao->responsavel }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">
                                    {{ $movimentacao->observacoes ?: '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhuma movimentação registrada ainda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($movimentacoes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $movimentacoes->links() }}
            </div>
        @endif
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
        min-width: 800px !important;
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

