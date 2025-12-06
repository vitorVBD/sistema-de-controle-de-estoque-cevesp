@extends('layouts.app')

@section('title', 'Registrar Saída')

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block cursor-pointer">
            ← Voltar ao Dashboard
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Registrar Saída de Item</h1>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6">
        <form action="{{ route('movimentacoes.saida') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="item_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Item <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="item_id"
                        id="item_id"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('item_id') border-red-500 @enderror">
                        <option value="">Selecione um item</option>
                        @foreach($itens as $item)
                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }} data-quantidade="{{ $item->quantidade_atual }}">
                                {{ $item->nome }} ({{ $item->quantidade_atual }} {{ $item->unidade_medida }} disponível)
                            </option>
                        @endforeach
                    </select>
                    @error('item_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <div id="quantidade-disponivel" class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-md hidden">
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">Quantidade disponível:</span>
                            <span id="quantidade-disponivel-valor" class="font-semibold"></span>
                        </p>
                    </div>
                </div>

                <div>
                    <label for="quantidade" class="block text-sm font-medium text-gray-700 mb-1">
                        Quantidade a Retirar <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="quantidade"
                        id="quantidade"
                        value="{{ old('quantidade') }}"
                        min="1"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('quantidade') border-red-500 @enderror">
                    @error('quantidade')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-1">
                        Observações
                    </label>
                    <textarea
                        name="observacoes"
                        id="observacoes"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('observacoes') border-red-500 @enderror">{{ old('observacoes') }}</textarea>
                    @error('observacoes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition duration-150 ease-in-out cursor-pointer">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-150 ease-in-out cursor-pointer">
                    Confirmar Saída
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemSelect = document.getElementById('item_id');
        const quantidadeDisponivelDiv = document.getElementById('quantidade-disponivel');
        const quantidadeDisponivelValor = document.getElementById('quantidade-disponivel-valor');
        const quantidadeInput = document.getElementById('quantidade');

        itemSelect.addEventListener('change', function() {
            const selectedOption = itemSelect.options[itemSelect.selectedIndex];
            const quantidade = selectedOption.getAttribute('data-quantidade');

            if (quantidade) {
                quantidadeDisponivelValor.textContent = quantidade;
                quantidadeDisponivelDiv.classList.remove('hidden');
                quantidadeInput.setAttribute('max', quantidade);
            } else {
                quantidadeDisponivelDiv.classList.add('hidden');
                quantidadeInput.removeAttribute('max');
            }
        });
    });
</script>
@endsection

