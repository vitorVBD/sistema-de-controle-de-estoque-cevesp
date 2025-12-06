@extends('layouts.app')

@section('title', 'Notificações')

@section('content')
<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Notificações</h1>
        <form action="{{ route('notificacoes.marcar-todas-lidas') }}" method="POST" class="inline w-full sm:w-auto">
            @csrf
            <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out cursor-pointer">
                Marcar Todas como Lidas
            </button>
        </form>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('notificacoes.index') }}" class="flex flex-wrap gap-4">
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Alerta</label>
                <select name="tipo" id="tipo" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    <option value="">Todos</option>
                    <option value="validade" {{ request('tipo') === 'validade' ? 'selected' : '' }}>Validade</option>
                    <option value="estoque_minimo" {{ request('tipo') === 'estoque_minimo' ? 'selected' : '' }}>Estoque Mínimo</option>
                    <option value="sugestao_mmc" {{ request('tipo') === 'sugestao_mmc' ? 'selected' : '' }}>Sugestão MMC</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                    <option value="">Todos</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Não Lidas</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Lidas</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out cursor-pointer">
                    Filtrar
                </button>
                @if(request()->has('tipo') || request()->has('status'))
                    <a href="{{ route('notificacoes.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out cursor-pointer">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Lista de Notificações -->
    <div class="bg-white shadow-sm rounded-lg" style="overflow: visible;">
        <div class="table-container-scroll" style="overflow-x: auto !important; overflow-y: visible !important; -webkit-overflow-scrolling: touch !important; width: 100% !important; display: block !important;">
            <table class="min-w-full divide-y divide-gray-200" style="min-width: 900px;">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Item
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mensagem
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($notificacoes as $notificacao)
                        <tr class="{{ !$notificacao->is_lida ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($notificacao->tipo_alerta === 'validade')
                                        <span class="text-2xl">📅</span>
                                    @elseif($notificacao->tipo_alerta === 'estoque_minimo')
                                        <span class="text-2xl">⚠️</span>
                                    @else
                                        <span class="text-2xl">📊</span>
                                    @endif
                                    <span class="ml-2 text-sm font-medium text-gray-900">
                                        @if($notificacao->tipo_alerta === 'validade')
                                            Validade
                                        @elseif($notificacao->tipo_alerta === 'estoque_minimo')
                                            Estoque Mínimo
                                        @else
                                            Sugestão MMC
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($notificacao->item)
                                    <a href="{{ route('itens.lotes', $notificacao->item) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900 cursor-pointer">
                                        {{ $notificacao->item->nome }}
                                    </a>
                                @else
                                    <span class="text-sm text-gray-500">Item removido</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $notificacao->mensagem }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ $notificacao->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($notificacao->is_lida)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Lida
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Não Lida
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if(!$notificacao->is_lida)
                                    <form action="{{ route('notificacoes.marcar-lida', $notificacao) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-900 cursor-pointer">
                                            Marcar como lida
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nenhuma notificação encontrada.
                            </td>
                        </tr>
                    @endforelse
            </tbody>
        </table>
        </div>

        <!-- Paginação -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $notificacoes->links() }}
        </div>
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
        min-width: 900px !important;
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

