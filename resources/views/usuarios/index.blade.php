@extends('layouts.app')

@section('title', 'Gerenciar Usuários')

@section('content')
<div>
    <div class="flex justify-between items-center mb-6 px-4 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gerenciar Usuários</h1>
            <p class="text-sm text-gray-600 mt-1">Apenas administradores podem gerenciar usuários</p>
        </div>
        <!-- Botão Desktop (oculto em telas menores) -->
        <a href="{{ route('usuarios.create') }}" class="hidden md:block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out cursor-pointer">
            + Novo Usuário
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
                        Nome
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Username
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Função
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($usuarios as $usuario)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $usuario->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $usuario->username }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $usuario->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($usuario->role === 'administrador' || $usuario->role === 'admin')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    Administrador
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Usuário
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('usuarios.edit', $usuario) }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full transition duration-150 ease-in-out inline-block cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @if($usuario->id !== Auth::id())
                                    <button
                                        onclick="abrirModalExcluir({{ $usuario->id }}, {!! json_encode($usuario->name) !!})"
                                        class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full transition duration-150 ease-in-out cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            Nenhum usuário cadastrado ainda. <a href="{{ route('usuarios.create') }}" class="text-blue-600 hover:text-blue-900 cursor-pointer">Criar primeiro usuário</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <!-- Botão Flutuante Mobile (apenas em telas menores) -->
    <div class="md:hidden fixed bottom-6 right-6 z-30">
        <a href="{{ route('usuarios.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition duration-150 ease-in-out cursor-pointer inline-block">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </a>
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
                            Tem certeza que deseja excluir o usuário <strong id="modal_excluir_usuario_nome"></strong>?
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

@endsection

@section('scripts')
<script>
    function abrirModalExcluir(usuarioId, usuarioNome) {
        document.getElementById('modal_excluir_usuario_nome').textContent = usuarioNome;
        const baseUrl = '{{ url("/usuarios") }}';
        document.getElementById('formExcluir').action = baseUrl + '/' + usuarioId;
        document.getElementById('modalExcluir').classList.remove('hidden');
    }

    function fecharModalExcluir() {
        document.getElementById('modalExcluir').classList.add('hidden');
    }

    // Fechar modal ao clicar fora
    window.onclick = function(event) {
        const modalExcluir = document.getElementById('modalExcluir');
        if (event.target == modalExcluir) {
            fecharModalExcluir();
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

