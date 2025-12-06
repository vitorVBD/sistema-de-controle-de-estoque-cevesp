@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-0">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Dashboard</h1>

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

    <!-- Cards de Alerta -->
    @if($itensEstoqueMinimo > 0)
        <!-- Estado Vermelho: Itens no mínimo ou abaixo -->
        <a href="{{ route('itens.estoque-baixo') }}" class="block mb-6 cursor-pointer">
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm hover:bg-red-100 transition duration-150 ease-in-out">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-3xl">🔴</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-red-800">
                            {{ $itensEstoqueMinimo }} {{ $itensEstoqueMinimo == 1 ? 'Item Precisa' : 'Itens Precisam' }} de Reposição
                        </h3>
                        <p class="text-sm text-red-700 mt-1">
                            Clique aqui para ver os itens que precisam de reposição urgente
                        </p>
                    </div>
                </div>
            </div>
        </a>
    @endif

    @if($itensAproximando > 0)
        <!-- Estado Amarelo: Itens se aproximando do estoque mínimo -->
        <a href="{{ route('itens.estoque-aproximando') }}" class="block mb-6 cursor-pointer">
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm hover:bg-yellow-100 transition duration-150 ease-in-out">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-3xl">⚠️</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-yellow-800">
                            {{ $itensAproximando }} {{ $itensAproximando == 1 ? 'Item vai precisar' : 'Itens vão precisar' }} de reposição em breve
                        </h3>
                        <p class="text-sm text-yellow-700 mt-1">
                            Clique aqui para ver os itens que estão se aproximando do estoque mínimo
                        </p>
                    </div>
                </div>
            </div>
        </a>
    @endif

    @if($itensEstoqueMinimo == 0 && $itensAproximando == 0)
        <!-- Estado Verde: Todos os itens em estoque -->
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <span class="text-3xl">✅</span>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-green-800">
                        Todos os itens em estoque
                    </h3>
                    <p class="text-sm text-green-700 mt-1">
                        Nenhum item precisa de reposição no momento
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-6">
        <a href="{{ route('itens.index') }}" class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition duration-150 ease-in-out cursor-pointer block dashboard-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <span class="text-white text-2xl">📦</span>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total de Itens</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $totalItens }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('itens.estoque-normal') }}" class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition duration-150 ease-in-out cursor-pointer block dashboard-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <span class="text-white text-2xl">✅</span>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Itens em Estoque Normal</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $itensEstoqueNormal }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('movimentacoes.index') }}" class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition duration-150 ease-in-out cursor-pointer block dashboard-card">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                        <span class="text-white text-2xl">📊</span>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total de Movimentações</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $totalMovimentacoes }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Ações Rápidas -->
    <div class="bg-white shadow-sm rounded-lg p-6 dashboard-card">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Ações Rápidas</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('itens.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out cursor-pointer">
                <span class="text-2xl mr-3">➕</span>
                <div>
                    <h3 class="font-medium text-gray-900">Adicionar Item</h3>
                    <p class="text-sm text-gray-500">Cadastrar novo item no estoque</p>
                </div>
            </a>
            <a href="{{ route('movimentacoes.create-entrada') }}" class="flex items-center p-4 border border-green-200 bg-green-50 rounded-lg hover:bg-green-100 transition duration-150 ease-in-out cursor-pointer">
                <span class="text-2xl mr-3">📥</span>
                <div>
                    <h3 class="font-medium text-green-900">Registrar Entrada</h3>
                    <p class="text-sm text-green-700">Adicionar itens ao estoque</p>
                </div>
            </a>
            <a href="{{ route('movimentacoes.create-saida') }}" class="flex items-center p-4 border border-red-200 bg-red-50 rounded-lg hover:bg-red-100 transition duration-150 ease-in-out cursor-pointer">
                <span class="text-2xl mr-3">📤</span>
                <div>
                    <h3 class="font-medium text-red-900">Registrar Saída</h3>
                    <p class="text-sm text-red-700">Retirar itens do estoque</p>
                </div>
            </a>
            @if($itensEstoqueMinimo > 0)
                <a href="{{ route('itens.estoque-baixo') }}" class="flex items-center p-4 border border-red-200 bg-red-50 rounded-lg hover:bg-red-100 transition duration-150 ease-in-out cursor-pointer">
                    <span class="text-2xl mr-3">🔴</span>
                    <div>
                        <h3 class="font-medium text-red-900">Itens com Estoque Baixo</h3>
                        <p class="text-sm text-red-700">Ver itens que precisam de reposição</p>
                    </div>
                </a>
            @endif
        </div>
    </div>

    <!-- Relatórios Rápidos -->
    <div class="bg-white shadow-sm rounded-lg p-6 dashboard-card mt-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Relatórios</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <a href="{{ route('relatorios.estoque-critico') }}" target="_blank" class="flex items-center p-4 border border-red-200 bg-red-50 rounded-lg hover:bg-red-100 transition duration-150 ease-in-out cursor-pointer">
                <span class="text-2xl mr-3">📄</span>
                <div class="flex-1">
                    <h3 class="font-medium text-red-900">Relatório de Estoque Crítico</h3>
                    <p class="text-sm text-red-700">Gerar PDF com itens em estoque crítico</p>
                </div>
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
            </a>
            <a href="{{ route('relatorios.validade') }}" target="_blank" class="flex items-center p-4 border border-yellow-200 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition duration-150 ease-in-out cursor-pointer">
                <span class="text-2xl mr-3">📄</span>
                <div class="flex-1">
                    <h3 class="font-medium text-yellow-900">Relatório de Validade Próxima</h3>
                    <p class="text-sm text-yellow-700">Gerar PDF com itens que vencem em 90 dias</p>
                </div>
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection


