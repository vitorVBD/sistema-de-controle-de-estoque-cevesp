@extends('layouts.app')

@section('title', 'Listagem de Itens')

@section('content')
<div class="px-4 sm:px-6 lg:px-0 w-full max-w-full itens-page-container">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                @if(request()->routeIs('itens.estoque-baixo'))
                    Itens com Estoque Baixo
                @elseif(request()->routeIs('itens.estoque-normal'))
                    Itens com Estoque Normal
                @else
                    Itens em Estoque
                @endif
            </h1>
            @if(request()->routeIs('itens.estoque-baixo'))
                <p class="text-sm text-red-700 mt-1">Listagem ordenada por urgência de reposição</p>
            @elseif(request()->routeIs('itens.estoque-normal'))
                <p class="text-sm text-green-700 mt-1">Listagem ordenada por proximidade ao estoque mínimo</p>
            @endif
        </div>
        <!-- Botões Desktop (ocultos em telas menores) -->
        <div class="hidden md:flex space-x-2">
            @if(request()->routeIs('itens.estoque-baixo') || request()->routeIs('itens.estoque-normal'))
                <a href="{{ route('itens.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out cursor-pointer">
                    Ver Todos
                </a>
            @endif
            <a href="{{ route('relatorios.estoque-critico') }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out cursor-pointer flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Estoque Crítico
            </a>
            <a href="{{ route('relatorios.validade') }}" target="_blank" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out cursor-pointer flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Validade
            </a>
            <a href="{{ route('itens.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out cursor-pointer">
                + Novo Item
            </a>
        </div>
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

    <div class="bg-white shadow-sm rounded-lg" style="overflow: visible; position: relative;">
        <div class="table-container-scroll" style="overflow-x: auto !important; overflow-y: visible !important; -webkit-overflow-scrolling: touch !important; width: 100% !important; max-width: 100vw !important; display: block !important; position: relative !important;">
            <table class="min-w-full divide-y divide-gray-200" style="min-width: 1000px !important; width: 100%;">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nome
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Quantidade Atual
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estoque Mínimo
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Unidade
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        MMC / Sugestão
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
                @forelse($itens as $item)
                    <tr class="{{ $item->estaEmEstoqueMinimo() ? 'bg-red-100 hover:bg-red-200' : ($item->estaAproximandoEstoqueMinimo() ? 'bg-yellow-50 hover:bg-yellow-100' : 'hover:bg-gray-50') }}">
                        <td class="px-6 py-4 whitespace-nowrap cursor-pointer" onclick="window.location.href='{{ route('itens.lotes', $item) }}'">
                            <div class="text-sm font-medium text-gray-900">{{ $item->nome }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap cursor-pointer" onclick="window.location.href='{{ route('itens.lotes', $item) }}'">
                            <div class="text-sm text-gray-900">{{ $item->quantidade_atual }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap cursor-pointer" onclick="window.location.href='{{ route('itens.lotes', $item) }}'">
                            <div class="text-sm text-gray-900">{{ $item->estoque_minimo }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap cursor-pointer" onclick="window.location.href='{{ route('itens.lotes', $item) }}'">
                            <div class="text-sm text-gray-500">{{ $item->unidade_medida }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap cursor-pointer" onclick="window.location.href='{{ route('itens.lotes', $item) }}'">
                            <div class="relative group">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">
                                        MMC: {{ number_format($item->media_mensal_consumo, 1) }}
                                    </div>
                                    @if($item->estoqueMinimoAbaixoSugerido())
                                        <div class="text-xs text-orange-600 font-semibold">
                                            Estoque mínimo sugerido: {{ $item->estoque_minimo_sugerido }}
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-500">
                                            Estoque mínimo sugerido: {{ $item->estoque_minimo_sugerido }}
                                        </div>
                                    @endif
                                </div>
                                <span class="absolute left-full ml-2 top-1/2 -translate-y-1/2 px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                    MMC: Média Mensal de Consumo (últimos 90 dias)<br>
                                    Sugestão: MMC × 1.5 (1,5 mês de estoque)
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap cursor-pointer" onclick="window.location.href='{{ route('itens.lotes', $item) }}'">
                            @if($item->estaEmEstoqueMinimo())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    🔴 Estoque Baixo
                                </span>
                            @elseif($item->estaAproximandoEstoqueMinimo())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    ⚠️ Aproximando do Mínimo
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    ✅ Normal
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" onclick="event.stopPropagation()">
                            <div class="flex justify-end space-x-2">
                                <div class="relative group">
                                    <button
                                        onclick="event.stopPropagation(); abrirModalEntrada({{ $item->id }}, '{{ $item->nome }}')"
                                        class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-full transition duration-150 ease-in-out cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                    <span class="absolute right-full mr-2 top-1/2 -translate-y-1/2 px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                        Registrar Entrada
                                    </span>
                                </div>
                                <div class="relative group">
                                    <button
                                        onclick="event.stopPropagation(); abrirModalSaida({{ $item->id }}, '{{ $item->nome }}', {{ $item->quantidade_atual }})"
                                        class="bg-orange-600 hover:bg-orange-700 text-white p-2 rounded-full transition duration-150 ease-in-out cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span class="absolute right-full mr-2 top-1/2 -translate-y-1/2 px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                        Registrar Saída
                                    </span>
                                </div>
                                <div class="relative group">
                                    <a href="{{ route('itens.edit', $item) }}"
                                       onclick="event.stopPropagation()"
                                       class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full transition duration-150 ease-in-out inline-block cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <span class="absolute right-full mr-2 top-1/2 -translate-y-1/2 px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                        Editar Item
                                    </span>
                                </div>
                                <div class="relative group">
                                    <button
                                        onclick="event.stopPropagation(); abrirModalExcluir({{ $item->id }}, '{{ $item->nome }}')"
                                        class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full transition duration-150 ease-in-out cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    <span class="absolute right-full mr-2 top-1/2 -translate-y-1/2 px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                        Excluir Item
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            Nenhum item cadastrado ainda. <a href="{{ route('itens.create') }}" class="text-blue-600 hover:text-blue-900 cursor-pointer">Criar primeiro item</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <!-- Botões Flutuantes Mobile (apenas em telas menores) -->
    <div class="md:hidden fixed bottom-6 right-6 z-30 flex flex-col items-end space-y-4">
        <!-- Botão de Relatórios -->
        <div class="relative">
            <button onclick="abrirModalRelatorios()" class="bg-yellow-600 hover:bg-yellow-700 text-white rounded-full p-4 shadow-lg transition duration-150 ease-in-out cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </button>
        </div>

        <!-- Botão de Criar Item -->
        <a href="{{ route('itens.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition duration-150 ease-in-out cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </a>
    </div>

    <!-- Modal de Seleção de Relatórios -->
    <div id="modalRelatorios" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-1/2 -translate-y-1/2 mx-auto p-5 border w-80 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Selecionar Relatório</h3>
                    <button onclick="fecharModalRelatorios()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('relatorios.estoque-critico') }}" target="_blank" onclick="fecharModalRelatorios()" class="flex items-center p-4 border border-red-200 bg-red-50 rounded-lg hover:bg-red-100 transition duration-150 ease-in-out cursor-pointer">
                        <div class="flex-shrink-0 bg-red-600 rounded-md p-2 mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-red-900">Estoque Crítico</h4>
                            <p class="text-sm text-red-700">Itens em estoque crítico</p>
                        </div>
                    </a>

                    <a href="{{ route('relatorios.validade') }}" target="_blank" onclick="fecharModalRelatorios()" class="flex items-center p-4 border border-yellow-200 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition duration-150 ease-in-out cursor-pointer">
                        <div class="flex-shrink-0 bg-yellow-600 rounded-md p-2 mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-yellow-900">Validade Próxima</h4>
                            <p class="text-sm text-yellow-700">Itens que vencem em 90 dias</p>
                        </div>
                    </a>
                </div>

                <div class="mt-4 flex justify-end">
                    <button
                        type="button"
                        onclick="fecharModalRelatorios()"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition duration-150 ease-in-out cursor-pointer">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Entrada -->
<div id="modalEntrada" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Registrar Entrada</h3>
                <button onclick="fecharModalEntrada()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>

            <form id="formEntrada" action="{{ route('movimentacoes.entrada') }}" method="POST">
                @csrf
                <input type="hidden" name="item_id" id="modal_entrada_item_id">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                    <input type="text" id="modal_entrada_item_nome" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">
                </div>

                <div class="mb-4">
                    <label for="quantidade_entrada" class="block text-sm font-medium text-gray-700 mb-1">
                        Quantidade a Adicionar <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="quantidade"
                        id="quantidade_entrada"
                        min="1"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <div class="mb-4">
                    <label for="data_validade" class="block text-sm font-medium text-gray-700 mb-1">
                        Data de Validade
                    </label>
                    <input
                        type="date"
                        name="data_validade"
                        id="data_validade"
                        min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Opcional: Se não informado, será usado 1 ano à frente como padrão</p>
                </div>

                <div class="mb-4">
                    <label for="observacoes_entrada" class="block text-sm font-medium text-gray-700 mb-1">
                        Observações
                    </label>
                    <textarea
                        name="observacoes"
                        id="observacoes_entrada"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        onclick="fecharModalEntrada()"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition duration-150 ease-in-out cursor-pointer">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-150 ease-in-out cursor-pointer">
                        Confirmar Entrada
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Confirmar Exclusão -->
<div id="modalExcluir" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Confirmar Exclusão</h3>
                <button onclick="fecharModalExcluir()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>

            <div class="mb-4">
                <div class="flex items-center mb-3">
                    <div class="flex-shrink-0">
                        <span class="text-4xl">⚠️</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-700">
                            Tem certeza que deseja excluir o item <strong id="modal_excluir_item_nome"></strong>?
                        </p>
                        <p class="text-xs text-red-600 mt-1">
                            Esta ação não pode ser desfeita.
                        </p>
                    </div>
                </div>
            </div>

            <form id="formExcluir" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        onclick="fecharModalExcluir()"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition duration-150 ease-in-out cursor-pointer">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-150 ease-in-out cursor-pointer">
                        Excluir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Registrar Saída -->
<div id="modalSaida" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Registrar Saída</h3>
                <button onclick="fecharModalSaida()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>

            <form id="formSaida" action="{{ route('movimentacoes.saida') }}" method="POST">
                @csrf
                <input type="hidden" name="item_id" id="modal_item_id">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                    <input type="text" id="modal_item_nome" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade Disponível</label>
                    <input type="text" id="modal_item_quantidade" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">
                </div>

                <div class="mb-4">
                    <label for="quantidade" class="block text-sm font-medium text-gray-700 mb-1">
                        Quantidade a Retirar <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="quantidade"
                        id="quantidade"
                        min="1"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="mb-4">
                    <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-1">
                        Observações
                    </label>
                    <textarea
                        name="observacoes"
                        id="observacoes"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        onclick="fecharModalSaida()"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition duration-150 ease-in-out cursor-pointer">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition duration-150 ease-in-out cursor-pointer">
                        Confirmar Saída
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .itens-page-container {
        position: relative;
    }

    /* Permitir scroll horizontal apenas no container da tabela */
    .table-container-scroll {
        overflow-x: auto !important;
        overflow-y: visible !important;
        -webkit-overflow-scrolling: touch !important;
        width: 100% !important;
        max-width: 100vw !important;
        display: block !important;
        position: relative !important;
        /* Criar um novo contexto de stacking para permitir scroll */
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
    }

    /* Garantir que a tabela mantenha sua largura mínima */
    .table-container-scroll table {
        min-width: 1000px !important;
        width: 100%;
    }

    /* Forçar que o container pai não bloqueie */
    .itens-page-container .bg-white.shadow-sm.rounded-lg {
        overflow: visible !important;
    }

    /* Sobrescrever overflow-x-hidden do main usando especificidade maior */
    main.itens-page-main,
    main:has(.itens-page-container) {
        overflow-x: visible !important;
    }
</style>

@endsection

@section('scripts')
<script>
    function abrirModalEntrada(itemId, itemNome) {
        document.getElementById('modal_entrada_item_id').value = itemId;
        document.getElementById('modal_entrada_item_nome').value = itemNome;
        document.getElementById('quantidade_entrada').value = '';
        document.getElementById('data_validade').value = '';
        document.getElementById('observacoes_entrada').value = '';
        document.getElementById('modalEntrada').classList.remove('hidden');
    }

    function fecharModalEntrada() {
        document.getElementById('modalEntrada').classList.add('hidden');
    }

    function abrirModalSaida(itemId, itemNome, quantidadeAtual) {
        document.getElementById('modal_item_id').value = itemId;
        document.getElementById('modal_item_nome').value = itemNome;
        document.getElementById('modal_item_quantidade').value = quantidadeAtual;
        document.getElementById('quantidade').max = quantidadeAtual;
        document.getElementById('quantidade').value = '';
        document.getElementById('observacoes').value = '';
        document.getElementById('modalSaida').classList.remove('hidden');
    }

    function fecharModalSaida() {
        document.getElementById('modalSaida').classList.add('hidden');
    }

    function abrirModalExcluir(itemId, itemNome) {
        document.getElementById('modal_excluir_item_nome').textContent = itemNome;
        const baseUrl = '{{ url("/itens") }}';
        document.getElementById('formExcluir').action = baseUrl + '/' + itemId;
        document.getElementById('modalExcluir').classList.remove('hidden');
    }

    function fecharModalExcluir() {
        document.getElementById('modalExcluir').classList.add('hidden');
    }

    function abrirModalRelatorios() {
        document.getElementById('modalRelatorios').classList.remove('hidden');
    }

    function fecharModalRelatorios() {
        document.getElementById('modalRelatorios').classList.add('hidden');
    }

    // Fechar modais ao clicar fora
    window.onclick = function(event) {
        const modalEntrada = document.getElementById('modalEntrada');
        const modalSaida = document.getElementById('modalSaida');
        const modalExcluir = document.getElementById('modalExcluir');
        const modalRelatorios = document.getElementById('modalRelatorios');

        if (event.target == modalEntrada) {
            fecharModalEntrada();
        }
        if (event.target == modalSaida) {
            fecharModalSaida();
        }
        if (event.target == modalExcluir) {
            fecharModalExcluir();
        }
        if (event.target == modalRelatorios) {
            fecharModalRelatorios();
        }
    }

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


