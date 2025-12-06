@extends('layouts.app')

@section('title', 'Movimentações de ' . $item->nome)

@section('content')
<div>
    <div class="flex justify-between items-center mb-6 px-4 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Movimentações de {{ $item->nome }}</h1>
            <p class="text-sm text-gray-600 mt-1">Histórico completo de entradas e saídas</p>
        </div>
        <a href="{{ route('itens.lotes', $item) }}" class="text-blue-600 hover:text-blue-900 cursor-pointer">
            ← Voltar para Lotes
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

    <div class="bg-white shadow-sm rounded-lg" style="overflow: visible;">
        <div class="table-container-scroll" style="overflow-x: auto !important; overflow-y: visible !important; -webkit-overflow-scrolling: touch !important; width: 100% !important; display: block !important;">
            <table class="min-w-full divide-y divide-gray-200" style="min-width: 800px;">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data/Hora
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
                                    <span class="text-gray-500 ml-1">{{ $item->unidade_medida }}</span>
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
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhuma movimentação registrada para este item.
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

