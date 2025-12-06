@extends('layouts.app')

@section('title', 'Criar Novo Item')

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('itens.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block cursor-pointer">
            ← Voltar para listagem
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Criar Novo Item</h1>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6">
        <form action="{{ route('itens.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">
                        Nome do Item <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="nome"
                        id="nome"
                        value="{{ old('nome') }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nome') border-red-500 @enderror">
                    @error('nome')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantidade_atual" class="block text-sm font-medium text-gray-700 mb-1">
                        Quantidade Atual <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="quantidade_atual"
                        id="quantidade_atual"
                        value="{{ old('quantidade_atual', 0) }}"
                        min="0"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('quantidade_atual') border-red-500 @enderror">
                    @error('quantidade_atual')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estoque_minimo" class="block text-sm font-medium text-gray-700 mb-1">
                        Estoque Mínimo <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="estoque_minimo"
                        id="estoque_minimo"
                        value="{{ old('estoque_minimo', 5) }}"
                        min="0"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('estoque_minimo') border-red-500 @enderror">
                    @error('estoque_minimo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="unidade_medida" class="block text-sm font-medium text-gray-700 mb-1">
                        Unidade de Medida <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="unidade_medida"
                        id="unidade_medida"
                        value="{{ old('unidade_medida') }}"
                        placeholder="Ex: unidades, caixas, litros, kg..."
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('unidade_medida') border-red-500 @enderror">
                    @error('unidade_medida')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2" id="validade-container">
                    <label for="data_validade_inicial" class="block text-sm font-medium text-gray-700 mb-1">
                        Data de Validade (para estoque inicial)
                    </label>
                    <input
                        type="date"
                        name="data_validade_inicial"
                        id="data_validade_inicial"
                        value="{{ old('data_validade_inicial') }}"
                        min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('data_validade_inicial') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Opcional: Se não informado e quantidade > 0, será usado 1 ano à frente como padrão</p>
                    @error('data_validade_inicial')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('itens.index') }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition duration-150 ease-in-out cursor-pointer">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-150 ease-in-out cursor-pointer">
                    Criar Item
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

